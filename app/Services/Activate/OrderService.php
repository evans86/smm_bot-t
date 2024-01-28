<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Dto\BotFactory;
use App\Models\Order\Order;
use App\Models\User\User;
use App\Services\Activate\Order\OrderStrategy;
use App\Services\External\BottApi;
use App\Services\External\PartnerApi;
use App\Services\MainService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use RuntimeException;

class OrderService extends MainService
{

    /**
     * Создание заказа
     *
     * @param $request
     * @param BotDto $botDto
     * @param array $userData
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create($request, BotDto $botDto, array $userData)
    {
        //проверка наличия типа товара
        if (is_null($request->type))
            throw new \RuntimeException('Not Found Params: type');

        $type = $request->type;
        $orderStrategy = new OrderStrategy($type, $botDto);

        //получение пользователя
        $user = User::query()->where(['telegram_id' => $userData['user']['telegram_id']])->first();

        if (is_null($user))
            throw new RuntimeException('not found user');

        //получить товар который покупают, цену и название
        $service = $this->getServiceInform($botDto, $request->type_id);

        $service_name = $service['name'];
        $amountStart = (int)ceil(floatval($service['rate']) * 100); //цена за 1000

        if (is_null($request->quantity)) {
            //надо думать как здесь формировать цену
            $amountFinal = $amountStart + $amountStart * $botDto->percent / 100;
        } else {
            if (!is_null($request->runs)) {
                $amountQuantity = (($request->quantity * $amountStart) / 1000) * $request->runs;
                $amountFinal = $amountQuantity + $amountQuantity * $botDto->percent / 100;
            } else {
                $amountQuantity = ($request->quantity * $amountStart) / 1000;
                $amountFinal = $amountQuantity + $amountQuantity * $botDto->percent / 100;
            }
        }

        //проверка и списание баланса

        if ($amountFinal > $userData['money']) {
            throw new RuntimeException('Пополните баланс в боте');
        }

        $order_strategy = $orderStrategy->create($request);
        $order_id = intval($order_strategy['order']);

        //Попытаться списать баланс у пользователя
        $result = BottApi::subtractBalance($botDto, $userData, $amountFinal, 'Списание баланса SMM Module');

        if (!$result['result']) {
            throw new RuntimeException('При списании баланса произошла ошибка: ' . $result['message']);
        }

        BottApi::createOrder($botDto, $userData, $amountFinal,
            'Покупка "' . $service_name . '"');

        $data = [
            'user_id' => $user->id,
            'bot_id' => $botDto->id,
            'order_id' => $order_id,
            'start_count' => $request->quantity,
            'remains' => $request->quantity,
            'type' => $type,
            'type_name' => $service_name,
            'type_id' => $request->type_id,
            'link' => $request->link,
            'price' => $amountFinal,
            'status' => Order::CREATE_STATUS
        ];

        $order = Order::create($data);

        $result = [
            'id' => $order->order_id,
            'link' => $order->link,
            'cost' => $order->price,
            'type_name' => $order->type_name,
            'start_count' => $order->start_count,
            'remains' => $order->remains,
            'status' => $order->status,
            'created_at' => $order->created_at,
        ];

        return $result;
    }

    /**
     * Обновление информации при полуение orders
     *
     * @param BotDto $botDto
     * @param $user_id
     * @return void
     */
    public function updateOrders(BotDto $botDto, $user_id)
    {
        $statuses = [Order::CREATE_STATUS, Order::TO_PROCESS_STATUS, Order::WORK_STATUS];

        $orders = Order::query()->whereIn('status', $statuses)
            ->where(['user_id' => $user_id])
            ->where(['bot_id' => $botDto->id])
            ->get();

        foreach ($orders as $key => $order) {
            $this->order($botDto, $order);
        }
    }

    /**
     * Обновление информации заказа
     *
     * @param BotDto $botDto
     * @param Order $order
     * @return void
     */
    public function order(BotDto $botDto, Order $order)
    {
        $partnerApi = new PartnerApi($botDto->api_key);
        $request_order = $partnerApi->status($order->order_id);

        if ($order->created_at < Carbon::now()->subMonth())
            $status = Order::OLD_STATUS;
        else
            $status = $request_order['status'];

        isset($request_order['start_count']) ? $start_count = $request_order['start_count'] : $start_count = null;
        isset($request_order['remains']) ? $remains = $request_order['remains'] : $remains = null;

        $order->status = $status;
        $order->start_count = $start_count;
        $order->remains = $remains;

        $order->save();
    }

    /**
     * Получение информации о покупаемом сервисе
     *
     * @param BotDto $botDto
     * @param $service_id
     * @return array
     */
    public function getServiceInform(BotDto $botDto, $service_id)
    {
        $partnerApi = new PartnerApi($botDto->api_key);
        $services = $partnerApi->services();

        $result = [];

        foreach ($services as $key => $service) {
            if (($service['service'] == $service_id)) {

                $result = [
                    'type_id' => $service['service'],//ид типа товара
                    'name' => $service['name'],//название товара (поменять цену с учетом наценки)
                    'rate' => $service['rate'],//цена за 1000 единиц (посчитать с наценкой)
                    'type' => $service['type'],//с каким типом дальше создавать заказ
                ];
            }
        }

        return $result;
    }

    /**
     * Крон обновление информации заказов
     *
     * @return void
     */
    public function cronUpdateOrders()
    {
        try {
            $statuses = [Order::CREATE_STATUS, Order::TO_PROCESS_STATUS, Order::WORK_STATUS];
            $orders = Order::query()->whereIn('status', $statuses)->get();

            echo "START count:" . count($orders) . PHP_EOL;

            $start_text = "Smm Start count: " . count($orders) . PHP_EOL;
            $this->notifyTelegram($start_text);

            foreach ($orders as $key => $order) {
                echo "START" . $order->id . PHP_EOL;

                $botDto = BotFactory::fromEntity($order->bot);
                $this->order($botDto, $order);

                echo "FINISH" . $order->id . PHP_EOL;
            }

            $finish_text = "Smm finish count: " . count($orders) . PHP_EOL;
            $this->notifyTelegram($finish_text);

        } catch (\Exception $e) {
            $this->notifyTelegram('🔴' . $e->getMessage());
        }
    }

    public function notifyTelegram($text)
    {
        $client = new Client();

        $ids = [
            6715142449,
//            778591134
        ];

        //CronLogBot#1
        try {
            foreach ($ids as $id) {
                $client->post('https://api.telegram.org/bot6393333114:AAHaxf8M8lRdGXqq6OYwly6rFQy9HwPeHaY/sendMessage', [

                    RequestOptions::JSON => [
                        'chat_id' => $id,
                        'text' => $text,
                    ]
                ]);
            }
            //CronLogBot#2
        } catch (\Exception $e) {
            foreach ($ids as $id) {
                $client->post('https://api.telegram.org/bot6934899828:AAGg_f4k1LG_gcZNsNF2LHgdm7tym-1sYVg/sendMessage', [

                    RequestOptions::JSON => [
                        'chat_id' => $id,
                        'text' => $text,
                    ]
                ]);
            }
        }
    }
}

