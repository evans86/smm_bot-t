<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Dto\BotFactory;
use App\Helpers\ApiHelpers;
use App\Models\Country\Country;
use App\Models\Bot\Bot;
use App\Models\Order\Order;
use App\Models\User\User;
use App\Services\Activate\Order\OrderStrategy;
use App\Services\External\BottApi;
use App\Services\External\ActivApi;
use App\Services\External\PartnerApi;
use App\Services\MainService;
use RuntimeException;

class OrderService extends MainService
{

    /**
     * @param $request
     * @param BotDto $botDto
     * @param array|null $userData
     * @return array
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
//        $user = User::query()->where(['id' => 1])->first();

        if (is_null($user))
            throw new RuntimeException('not found user');

        //получить товар который покупают, цену и название

        $service = $this->getServiceInform($botDto, $request->type_id);
//        dd($service);

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

//        dd($amountFinal);

        //проверка и списание баланса

        if ($amountFinal > $userData['money']) {
            throw new RuntimeException('Пополните баланс в боте');
        }

//        //Попытаться списать баланс у пользователя
        $result = BottApi::subtractBalance($botDto, $userData, $amountFinal, 'Списание баланса SMM');

        if (!$result['result']) {
            throw new RuntimeException('При списании баланса произошла ошибка: ' . $result['message']);
        }

        BottApi::createOrder($botDto, $userData, $amountFinal,
            'Заказ SMM');

        $order_strategy = $orderStrategy->create($request);
        $order_id = intval($order_strategy['order']);

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
        $request_order = $partnerApi->status(42958657);
//        dd($request_order);

        $status = $request_order['status'];
        $start_count = $request_order['start_count'];
        $remains = $request_order['remains'];

        $order->status = $status;
        $order->start_count = $start_count;//убрать
        $order->remains = $remains;

        $order->save();


//        array:5 [▼
//            "charge" => "2.80"
//            "start_count" => "100"
//            "status" => "Pending"
//            "remains" => "100"
//            "currency" => "RUB"
//        ]
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
        $statuses = [Order::CREATE_STATUS, Order::TO_PROCESS_STATUS, Order::WORK_STATUS];
        $orders = Order::query()->whereIn('status', $statuses)->get();

        echo "START count:" . count($orders) . PHP_EOL;

        foreach ($orders as $key => $order) {
            echo "START" . $order->id . PHP_EOL;

            $botDto = BotFactory::fromEntity($order->bot);
            $this->order($botDto, $order);

            echo "FINISH" . $order->id . PHP_EOL;
        }
    }
}

