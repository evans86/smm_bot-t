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
            'start_count' => $request->quantity === '' ? null : (int)$request->quantity,
            'remains' => $request->quantity === '' ? null : (int)$request->quantity,
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
        $statuses = [
            Order::CREATE_STATUS,
            Order::TO_PROCESS_STATUS,
            Order::WORK_STATUS,
            Order::TO_PROCESSING_STATUS,
        ];

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
     * @param array $userData
     * @return void
     */
    public function order(BotDto $botDto, Order $order, array $userData = [])
    {
        try {
            $partnerApi = new PartnerApi($botDto->getEncryptedApiKey());
            $request_order = $partnerApi->status($order->order_id);

            $previousStatus = $order->status;

            if ($order->created_at < Carbon::now()->subMonth())
                $status = Order::OLD_STATUS;
            else
                $status = $request_order['status'];

            $start_count = isset($request_order['start_count']) && $request_order['start_count'] !== ''
                ? (int)$request_order['start_count']
                : null;

            $remains = isset($request_order['remains']) && $request_order['remains'] !== ''
                ? (int)$request_order['remains']
                : null;

            $order->status = $status;
            $order->start_count = $start_count ?? $order->start_count;
            $order->remains = $remains;

            $order->save();

            $this->refundIfNeeded($botDto, $order, $previousStatus, $userData);
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем выполнение
            \Log::error("Order update failed: {$e->getMessage()}", [
                'order_id' => $order->id,
                'bot_id' => $botDto->id
            ]);

            // Помечаем заказ как проблемный или оставляем как есть
            $order->status = Order::OLD_STATUS; // или другой статус
            $order->save();
        }
    }

    private function refundIfNeeded(BotDto $botDto, Order $order, ?string $previousStatus, array $userData = []): void
    {
        try {
            $this->processRefund($botDto, $order, $previousStatus, $userData);
        } catch (\Throwable $e) {
            \Log::error('SMM refund exception: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'provider_order_id' => $order->order_id,
            ]);
        }
    }

    private function processRefund(BotDto $botDto, Order $order, ?string $previousStatus, array $userData = []): void
    {
        if ($order->refunded_at) {
            return;
        }

        $refundStatuses = [Order::CANCEL_STATUS, Order::TO_PROCESS_STATUS];
        $alreadyClosed = [Order::CANCEL_STATUS, Order::TO_PROCESS_STATUS, Order::FINISH_STATUS, Order::OLD_STATUS];

        if (!in_array($order->status, $refundStatuses, true)) {
            return;
        }

        if (in_array((string)$previousStatus, $alreadyClosed, true)) {
            return;
        }

        $amount = $this->calculateRefundAmount($order);
        if ($amount <= 0) {
            $order->refunded_at = now();
            $order->save();
            return;
        }

        $userData = $this->resolveUserData($botDto, $order, $userData);
        if ($userData === []) {
            \Log::error('SMM refund skipped: user data not found', [
                'order_id' => $order->id,
                'provider_order_id' => $order->order_id,
            ]);
            return;
        }

        $result = BottApi::addBalance(
            $botDto,
            $userData,
            $amount,
            'Возврат SMM заказ #' . $order->order_id
        );

        if (!($result['result'] ?? false)) {
            \Log::error('SMM refund failed', [
                'order_id' => $order->id,
                'provider_order_id' => $order->order_id,
                'amount' => $amount,
                'message' => $result['message'] ?? 'unknown',
            ]);
            return;
        }

        $order->refunded_at = now();
        $order->save();
    }

    private function calculateRefundAmount(Order $order): int
    {
        $price = (int) round((float) $order->price);
        if ($price <= 0) {
            return 0;
        }

        $startCount = (int) $order->start_count;
        $remains = (int) $order->remains;

        if ($order->status === Order::TO_PROCESS_STATUS) {
            if ($startCount <= 0 || $remains <= 0) {
                return 0;
            }

            return (int) round($price * $remains / $startCount);
        }

        if ($startCount > 0 && $remains > 0 && $remains < $startCount) {
            return (int) round($price * $remains / $startCount);
        }

        return $price;
    }

    private function resolveUserData(BotDto $botDto, Order $order, array $userData): array
    {
        if (
            isset($userData['secret_user_key'], $userData['user']['telegram_id'])
            && $userData['secret_user_key'] !== ''
        ) {
            return $userData;
        }

        $user = $order->user ?: User::query()->find($order->user_id);
        if (!$user || !$user->telegram_id) {
            return [];
        }

        $result = BottApi::get((int) $user->telegram_id, $botDto->public_key, $botDto->private_key);
        if (!($result['result'] ?? false) || !is_array($result['data'] ?? null)) {
            return [];
        }

        return $result['data'];
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
        $partnerApi = new PartnerApi($botDto->getEncryptedApiKey());
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
        $logFile = storage_path('logs/order_cron.log');
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Cron started\n", FILE_APPEND);

        try {
            $statuses = [
                Order::CREATE_STATUS,
                Order::TO_PROCESS_STATUS,
                Order::WORK_STATUS,
                Order::TO_PROCESSING_STATUS,
            ];
            $ordersQuery = Order::query()
                ->where(function ($query) use ($statuses) {
                    $query->whereIn('status', $statuses)
                        ->orWhere(function ($unrefunded) {
                            $unrefunded->where('status', Order::CANCEL_STATUS)
                                ->whereNull('refunded_at');
                        });
                });
            $total = (clone $ordersQuery)->count();

            file_put_contents($logFile, "Total orders: $total\n", FILE_APPEND);
            $this->notifyTelegram("Smm Start: $total orders");

            $processed = 0;
            $errors = 0;

            $ordersQuery
                ->with(['bot', 'user'])
                ->chunk(50, function ($orders) use (&$processed, &$errors, $total, $logFile) {
                    foreach ($orders as $order) {
                        try {
                            if (!$order->bot) {
                                $errors++;
                                continue;
                            }
                            $botDto = BotFactory::fromEntity($order->bot);
                            $this->order($botDto, $order);
                            $processed++;

                        } catch (\Exception $e) {
                            $errors++;
                            file_put_contents($logFile, "Error order {$order->id}: " . $e->getMessage() . "\n", FILE_APPEND);
                        }

                        // Логируем прогресс
                        if ($processed % 100 === 0) {
                            $progress = "Processed: $processed/$total, Errors: $errors";
                            file_put_contents($logFile, $progress . "\n", FILE_APPEND);
                        }

                        unset($order, $botDto);
                        if ($processed % 10 === 0) {
                            gc_collect_cycles();
                        }
                    }
                });

            $message = "Smm finish: $processed processed, $errors errors";
            file_put_contents($logFile, $message . "\n", FILE_APPEND);
            $this->notifyTelegram($message);

        } catch (\Exception $e) {
            $errorMsg = "Cron Error: " . $e->getMessage();
            file_put_contents($logFile, $errorMsg . "\n", FILE_APPEND);
            $this->notifyTelegram('🔴 ' . $errorMsg);
        }
    }

//    public function cronUpdateOrders()
//    {
//        try {
//            $statuses = [Order::CREATE_STATUS, Order::TO_PROCESS_STATUS, Order::WORK_STATUS];
//            $orders = Order::query()->whereIn('status', $statuses)->get();
//
//            echo "START count:" . count($orders) . PHP_EOL;
//
//            $start_text = "Smm Start count: " . count($orders) . PHP_EOL;
//            $this->notifyTelegram($start_text);
//
//            foreach ($orders as $key => $order) {
//                echo "START" . $order->id . PHP_EOL;
//
//                $botDto = BotFactory::fromEntity($order->bot);
//                $this->order($botDto, $order);
//
//                echo "FINISH" . $order->id . PHP_EOL;
//            }
//
//            $finish_text = "Smm finish count: " . count($orders) . PHP_EOL;
//            $this->notifyTelegram($finish_text);
//
//        } catch (\Exception $e) {
//            $this->notifyTelegram('🔴' . $e->getMessage());
//        }
//    }

    public function notifyTelegram($text)
    {
        $client = new Client([
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Принудительно IPv4
            ],
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);

        $ids = [6715142449]; // Список chat_id
        $bots = [
            config('services.bot_api_keys.cron_log_bot_1'), // Основной бот
            config('services.bot_api_keys.cron_log_bot_2')  // Резервный бот
        ];

        // Если текст пустой, заменяем его на заглушку (или оставляем пустым)
        $message = ($text === '') ? '[Empty message]' : $text;

        $lastError = null;

        foreach ($bots as $botToken) {
            try {
                foreach ($ids as $id) {
                    $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        RequestOptions::JSON => [
                            'chat_id' => $id,
                            'text' => $message,
                        ],
                    ]);
                }
                return true; // Успешно отправлено
            } catch (\Exception $e) {
                $lastError = $e;
                continue; // Пробуем следующего бота
            }
        }

        // Если все боты не сработали, логируем ошибку (или просто игнорируем)
        error_log("Telegram send failed: " . $lastError->getMessage());
        return false;
    }
}

