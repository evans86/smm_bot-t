<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Dto\BotFactory;
use App\Models\Activate\SmsCountry;
use App\Models\Bot\SmsBot;
use App\Models\Order\SmsOrder;
use App\Models\User\SmsUser;
use App\Services\External\BottApi;
use App\Services\External\ActivApi;
use App\Services\MainService;
use RuntimeException;

class OrderService extends MainService
{
    /**
     * Создание заказа
     *
     * @param array $userData Сущность DTO from bott
     * @param BotDto $botDto
     * @param string $country_id
     * @return array
     * @throws \Exception
     */
    public function create(array $userData, BotDto $botDto, string $country_id): array
    {
        // Создать заказ по апи
        $smsActivate = new ActivApi($botDto->api_key, $botDto->resource_link);
        $user = SmsUser::query()->where(['telegram_id' => $userData['user']['telegram_id']])->first();
        if (is_null($user)) {
            throw new RuntimeException('not found user');
        }
        if (empty($user->service))
            throw new RuntimeException('Choose service pls');

        $serviceResult = $smsActivate->getNumberV2(
            $user->service,
            $country_id
        );
        $org_id = intval($serviceResult['activationId']);
        // Из него получить цену
        $amountStart = intval(floatval($serviceResult['activationCost']) * 100);
        $amountFinal = $amountStart + $amountStart * $botDto->percent / 100;
        if ($amountFinal > $userData['money']) {
            $serviceResult = $smsActivate->setStatus($org_id, SmsOrder::ACCESS_CANCEL);
            throw new RuntimeException('Пополните баланс в боте');
        }
        // Попытаться списать баланс у пользователя
        $result = BottApi::subtractBalance($botDto, $userData, $amountFinal, 'Списание баланса для номера '
            . $serviceResult['phoneNumber']);

        // Неудача отмена на сервисе
        if (!$result['result']) {
            $serviceResult = $smsActivate->setStatus($org_id, SmsOrder::ACCESS_CANCEL);
            throw new RuntimeException('При списании баланса произошла ошибка: ' . $result['message']);
        }

        // Удача создание заказа в бд
        $country = SmsCountry::query()->where(['org_id' => $country_id])->first();
        $dateTime = new \DateTime($serviceResult['activationTime']);
        $dateTime = $dateTime->format('U');
        $dateTime = intval($dateTime);
        $data = [
            'bot_id' => $botDto->id,
            'user_id' => $user->id,
            'service' => $user->service,
            'country_id' => $country->id,
            'org_id' => $org_id,
            'phone' => $serviceResult['phoneNumber'],
            'codes' => null,
            'status' => SmsOrder::STATUS_WAIT_CODE, //4
            'start_time' => $dateTime,
            'end_time' => $dateTime + 1177,
            'operator' => $serviceResult['activationOperator'],
            'price_final' => $amountFinal,
            'price_start' => $amountStart,
        ];

        $order = SmsOrder::create($data);
        $result = $smsActivate->setStatus($order, SmsOrder::ACCESS_RETRY_GET);
        $result = $this->getStatus($order->org_id, $botDto);

        $result = [
            'id' => $order->org_id,
            'phone' => $serviceResult['phoneNumber'],
            'time' => $dateTime,
            'status' => $order->status,
            'codes' => null,
            'country' => $country->org_id,
            'operator' => $serviceResult['activationOperator'],
            'service' => $user->service,
            'cost' => $amountFinal
        ];
        return $result;
    }

    /**
     * Отмена заказа со статусом 9
     *
     * @param array $userData
     * @param BotDto $botDto
     * @param SmsOrder $order
     * @return mixed
     */
    public function cancel(array $userData, BotDto $botDto, SmsOrder $order)
    {
        $smsActivate = new ActivApi($botDto->api_key, $botDto->resource_link);
        // Проверить уже отменёный
        if ($order->status == SmsOrder::STATUS_CANCEL)
            throw new RuntimeException('The order has already been canceled');
        if ($order->status == SmsOrder::STATUS_FINISH)
            throw new RuntimeException('The order has not been canceled, the number has been activated, Status 10');
        // Можно отменить только статус 4 и кодов нет
        if (!is_null($order->codes))
            throw new RuntimeException('The order has not been canceled, the number has been activated');

        // Обновить статус setStatus()
        $result = $smsActivate->setStatus($order->org_id, SmsOrder::ACCESS_CANCEL);
        // Проверить статус getStatus()
//        $result = $this->getStatus($order->org_id, $botDto);
//        if ($result != SmsOrder::STATUS_CANCEL)
//            //надо писать лог
//            throw new RuntimeException('При проверке статуса произошла ошибка, вернулся статус: ' . $result);

        $order->status = SmsOrder::STATUS_CANCEL;
        if ($order->save()) {
            // Он же возвращает баланс
            $amountFinal = $order->price_final;
            $result = BottApi::addBalance($botDto, $userData, $amountFinal, 'Возврат баланса, активация отменена');
        } else {
            throw new RuntimeException('Not save order');
        }
        return $result;
    }

    /**
     * Успешное завершение заказа со статусом 10
     *
     * @param BotDto $botDto
     * @param SmsOrder $order
     * @return int
     */
    public function confirm(BotDto $botDto, SmsOrder $order)
    {
        $smsActivate = new ActivApi($botDto->api_key, $botDto->resource_link);

        if ($order->status == SmsOrder::STATUS_CANCEL)
            throw new RuntimeException('The order has already been canceled');
        if (is_null($order->codes))
            throw new RuntimeException('Попытка установить несуществующий статус');
        if ($order->status == SmsOrder::STATUS_FINISH)
            throw new RuntimeException('The order has not been canceled, the number has been activated, Status 10');

        $result = $smsActivate->setStatus($order->org_id, SmsOrder::ACCESS_ACTIVATION);

        $result = $this->getStatus($order->org_id, $botDto);

        $order->status = SmsOrder::STATUS_FINISH;

        $order->save();

        return SmsOrder::STATUS_FINISH;
    }

    /**
     * Повторное получение СМС
     *
     * @param BotDto $botDto
     * @param SmsOrder $order
     * @return int
     */
    public function second(BotDto $botDto, SmsOrder $order)
    {
        $smsActivate = new ActivApi($botDto->api_key, $botDto->resource_link);

        if ($order->status == SmsOrder::STATUS_CANCEL)
            throw new RuntimeException('The order has already been canceled');
        if (is_null($order->codes))
            throw new RuntimeException('Попытка установить несуществующий статус');
        if ($order->status == SmsOrder::STATUS_FINISH)
            throw new RuntimeException('The order has not been canceled, the number has been activated, Status 10');

        $result = $smsActivate->setStatus($order->org_id, SmsOrder::ACCESS_READY);

        $result = $this->getStatus($order->org_id, $botDto);

        if ($result != SmsOrder::STATUS_WAIT_RETRY)
            throw new RuntimeException('При проверке статуса произошла ошибка, вернулся статус: ' . $result);

        $resultSet = $order->status = SmsOrder::STATUS_WAIT_RETRY;

        $order->save();
        return $resultSet;
    }

    /**
     * Получение активного заказа и обновление кодов
     *
     * @param array $userData
     * @param BotDto $botDto
     * @param SmsOrder $order
     * @return void
     */
    public function order(array $userData, BotDto $botDto, SmsOrder $order): void
    {
        switch ($order->status) {
            case SmsOrder::STATUS_CANCEL:
            case SmsOrder::STATUS_FINISH:
                break;
            case SmsOrder::STATUS_WAIT_CODE:
            case SmsOrder::STATUS_WAIT_RETRY:
                $resultStatus = $this->getStatus($order->org_id, $botDto);
                switch ($resultStatus) {
                    case SmsOrder::STATUS_FINISH:
                    case SmsOrder::STATUS_CANCEL:
                        break;
                    case SmsOrder::STATUS_OK:
                    case SmsOrder::STATUS_WAIT_CODE:
                    case SmsOrder::STATUS_WAIT_RETRY:
                        $smsActivate = new ActivApi($botDto->api_key, $botDto->resource_link);
                        $activateActiveOrders = $smsActivate->getActiveActivations();
                        if (key_exists('activeActivations', $activateActiveOrders)) {
                            $activateActiveOrders = $activateActiveOrders['activeActivations'];

                            foreach ($activateActiveOrders as $activateActiveOrder) {
                                $order_id = $activateActiveOrder['activationId'];
                                // Есть ли совпадение
                                if ($order_id == $order->org_id) {
                                    // Есть ли смс
                                    $sms = $activateActiveOrder['smsCode'];
                                    if (is_null($sms))
                                        break;
                                    $sms = json_encode($sms);
                                    if (is_null($order->codes)) {
                                        BottApi::createOrder($botDto, $userData, $order->price_final,
                                            'Заказ активации для номера ' . $order->phone .
                                            ' с смс: ' . $sms);
                                    }
                                    $order->codes = $sms;
                                    $order->status = $resultStatus;
                                    $order->save();
                                    break;
                                }
                            }
                        }
                        break;
                    default:
                        throw new RuntimeException('неизвестный статус: ' . $resultStatus);
                }
        }
    }

    /**
     * Крон обновление статусов
     *
     * @return void
     */
    public function cronUpdateStatus(): void
    {
        $statuses = [SmsOrder::STATUS_OK, SmsOrder::STATUS_WAIT_CODE, SmsOrder::STATUS_WAIT_RETRY];

        $orders = SmsOrder::query()->whereIn('status', $statuses)
            ->where('end_time', '<=', time())->get();

        echo "START count:" . count($orders) . PHP_EOL;
        foreach ($orders as $key => $order) {
            echo $order->id . PHP_EOL;
            $bot = SmsBot::query()->where(['id' => $order->bot_id])->first();

            $botDto = BotFactory::fromEntity($bot);
            $result = BottApi::get(
                $order->user->telegram_id,
                $botDto->public_key,
                $botDto->private_key
            );
            echo $order->id . PHP_EOL;


            if (is_null($order->codes)) {
                echo 'cancel_start' . PHP_EOL;
                $this->cancel(
                    $result['data'],
                    $botDto,
                    $order
                );
                echo 'cancel_finish' . PHP_EOL;
            } else {
                echo 'confirm_start' . PHP_EOL;
                $this->confirm(
                    $botDto,
                    $order
                );
                echo 'confirm_finish' . PHP_EOL;
            }
            echo "FINISH" . $order->id . PHP_EOL;

        }
    }

    /**
     * Статус заказа с сервиса
     *
     * @param $id
     * @param BotDto $botDto
     * @return mixed
     */
    public
    function getStatus($id, BotDto $botDto)
    {
        $smsActivate = new ActivApi($botDto->api_key, $botDto->resource_link);

        $serviceResult = $smsActivate->getStatus($id);
        return $serviceResult;
    }
}
