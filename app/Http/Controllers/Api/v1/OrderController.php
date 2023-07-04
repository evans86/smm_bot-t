<?php

namespace App\Http\Controllers\Api\v1;

use App\Dto\BotFactory;
use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\api\OrderResource;
use App\Models\Country\Country;
use App\Models\Bot\Bot;
use App\Models\Order\Order;
use App\Models\User\User;
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
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
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
//            if ($result['data']['money'] == 0) {
//                throw new RuntimeException('Пополните баланс в боте');
//            }

            $result = $this->orderService->create(
                $request,
                $botDto,
                $result['data']
            );

            return ApiHelpers::success($result);
        } catch (Exception $e) {
            return ApiHelpers::error($e->getMessage());
        }
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
            $user = User::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
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

            $result = OrderResource::collection(Order::query()->where(['user_id' => $user->id])->
            where(['bot_id' => $bot->id])->get());

            return ApiHelpers::success($result);

        } catch (Exception $e) {
            return ApiHelpers::errorNew($e->getMessage());
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
            $user = User::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->order_id))
                return ApiHelpers::error('Not found params: order_id');
            $order = Order::query()->where(['order_id' => $request->order_id])->first();
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
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

            $this->orderService->order(
                $botDto,
                $order,
                $result['data']
            );

            $order = Order::query()->where(['order_id' => $request->order_id])->first();
            return ApiHelpers::success(OrderResource::generateOrderArray($order));
        } catch (RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }
}
