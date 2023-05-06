<?php

namespace App\Http\Controllers\Api\v1;

use App\Dto\BotFactory;
use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\api\OrderResource;
use App\Models\Activate\SmsCountry;
use App\Models\Bot\SmsBot;
use App\Models\Order\SmsOrder;
use App\Models\User\SmsUser;
use App\Services\Activate\OrderService;
use App\Services\External\BottApi;
use Exception;
use Illuminate\Http\Request;
use RuntimeException;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    /**
     * Передача значений заказаов для пользователя
     *
     * Request[
     *  'user_id'
     *  'user_secret_key'
     *  'public_key'
     *
     * ]
     *
     * @param Request $request
     * @return array|string
     */
    public function orders(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
            $user = SmsUser::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = SmsBot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');

            $botDto = BotFactory::fromEntity($bot);
            $result = BottApi::checkUser(
                $request->user_id,
                $request->user_secret_key,
                $botDto->public_key,
                $botDto->private_key
            );
            if (!$result['result']) {
                throw new RuntimeException($result['message']);
            }

            $result = OrderResource::collection(SmsOrder::query()->where(['user_id' => $user->id])->
            where(['bot_id' => $bot->id])->get());

            return ApiHelpers::success($result);

        } catch (Exception $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * Создание заказа
     *
     * Request[
     *  'user_id'
     *  'country'
     *  'user_secret_key'
     *  'public_key'
     * ]
     * @param Request $request
     * @return array|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrder(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
            $user = SmsUser::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->country))
                return ApiHelpers::error('Not found params: country');
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = SmsBot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');
            $botDto = BotFactory::fromEntity($bot);
            $result = BottApi::checkUser(
                $request->user_id,
                $request->user_secret_key,
                $botDto->public_key,
                $botDto->private_key
            );
            if (!$result['result']) {
                throw new RuntimeException($result['message']);
            }
            if ($result['data']['money'] == 0) {
                throw new RuntimeException('Пополните баланс в боте');
            }
            $country = SmsCountry::query()->where(['org_id' => $request->country])->first();
            $service = $user->service;

            $result = $this->orderService->create(
                $result['data'],
                $botDto,
                $country->org_id,
            );

            return ApiHelpers::success($result);
        } catch (Exception $e) {
            return ApiHelpers::error($e->getMessage());
        }
    }

    /**
     * Получение активного заказа
     *
     * Request[
     *  'user_id'
     *  'order_id'
     *  'user_secret_key'
     *  'public_key'
     * ]
     *
     * @param Request $request
     * @return array|string
     */
    public function getOrder(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
            $user = SmsUser::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->order_id))
                return ApiHelpers::error('Not found params: order_id');
            $order = SmsOrder::query()->where(['org_id' => $request->order_id])->first();
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = SmsBot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);
            $result = BottApi::checkUser(
                $request->user_id,
                $request->user_secret_key,
                $botDto->public_key,
                $botDto->private_key
            );
            if (!$result['result']) {
                throw new RuntimeException($result['message']);
            }


            $this->orderService->order($result['data'], $botDto, $order);

            $order = SmsOrder::query()->where(['org_id' => $request->order_id])->first();
            return ApiHelpers::success(OrderResource::generateOrderArray($order));
        } catch (RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * Установить статус 3 (Запросить еще одну смс)
     *
     * Request[
     *  'user_id'
     *  'order_id'
     *  'public_key'
     * ]
     *
     * @param Request $request
     * @return array|string
     */
    public function secondSms(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
            $user = SmsUser::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->order_id))
                return ApiHelpers::error('Not found params: order_id');
            $order = SmsOrder::query()->where(['org_id' => $request->order_id])->first();
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = SmsBot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);
            $result = BottApi::checkUser(
                $request->user_id,
                $request->user_secret_key,
                $botDto->public_key,
                $botDto->private_key
            );
            if (!$result['result']) {
                throw new RuntimeException($result['message']);
            }

            $result = $this->orderService->second($botDto, $order);

            $order = SmsOrder::query()->where(['org_id' => $request->order_id])->first();
            return ApiHelpers::success(OrderResource::generateOrderArray($order));
        } catch (Exception $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * Установить статус 6 (Подтвердить SMS-код и завершить активацию)
     *
     * Request[
     *  'user_id'
     *  'order_id'
     *  'public_key'
     * ]
     *
     * @param Request $request
     * @return array|string
     */
    public function confirmOrder(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
            $user = SmsUser::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->order_id))
                return ApiHelpers::error('Not found params: order_id');
            $order = SmsOrder::query()->where(['org_id' => $request->order_id])->first();
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = SmsBot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);
            $result = BottApi::checkUser(
                $request->user_id,
                $request->user_secret_key,
                $botDto->public_key,
                $botDto->private_key
            );
            if (!$result['result']) {
                throw new RuntimeException($result['message']);
            }

            $result = $this->orderService->confirm($botDto, $order);

            $order = SmsOrder::query()->where(['org_id' => $request->order_id])->first();
            return ApiHelpers::success(OrderResource::generateOrderArray($order));
        } catch (Exception $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * Установить статус 8 (Отменить активацию (если номер Вам не подошел))
     *
     * Request[
     *  'user_id'
     *  'order_id'
     *  'public_key'
     * ]
     *
     * @param Request $request
     * @return array|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function closeOrder(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
            $user = SmsUser::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->order_id))
                return ApiHelpers::error('Not found params: order_id');
            $order = SmsOrder::query()->where(['org_id' => $request->order_id])->first();
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = SmsBot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);
            $result = BottApi::checkUser(
                $request->user_id,
                $request->user_secret_key,
                $botDto->public_key,
                $botDto->private_key
            );
            if (!$result['result']) {
                throw new RuntimeException($result['message']);
            }

            $result = $this->orderService->cancel($result['data'], $botDto, $order);

            $order = SmsOrder::query()->where(['org_id' => $request->order_id])->first();
            return ApiHelpers::success(OrderResource::generateOrderArray($order));
        } catch (Exception $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }
}
