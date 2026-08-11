<?php

namespace App\Http\Controllers\Api\v1;

use App\Dto\BotFactory;
use App\Helpers\ApiHelpers;
use App\Helpers\BotLogHelpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\api\OrderResource;
use App\Models\Bot\Bot;
use App\Models\Order\Order;
use App\Models\User\User;
use App\Services\Activate\OrderService;
use App\Services\External\BottApi;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    private OrderService $orderService;

    /**
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct()
    {
        // parent::__construct() adds auth middleware, while these API endpoints are public.
        $this->orderService = new OrderService();
    }

    /**
     * Создание заказа
     *
     * Request[
     *  'user_id'
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
            if (!is_numeric($request->user_id))
                return ApiHelpers::error('Invalid params: user_id');
            $user = User::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);
            if ($botDto->version != 3) {
                BotLogHelpers::notifyBotLog('(🟣KEY не поменял ' . $botDto->bot_id);
                return ApiHelpers::error('Fatal Error');
            }
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

            $result = $this->orderService->create(
                $request,
                $botDto,
                $result['data']
            );

            return ApiHelpers::success($result);
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (Throwable $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Create order error');
        }
    }

    /**
     * Получение списка заказов
     *
     * @param Request $request
     * @return array|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function orders(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
            if (!is_numeric($request->user_id))
                return ApiHelpers::error('Invalid params: user_id');
            $user = User::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');

            $botDto = BotFactory::fromEntity($bot);
            if ($botDto->version != 3) {
                BotLogHelpers::notifyBotLog('(🟣KEY не поменял ' . $botDto->bot_id);
                return ApiHelpers::error('Fatal Error');
            }
            $result = BottApi::checkUser(
                $request->user_id,
                $request->user_secret_key,
                $botDto->public_key,
                $botDto->private_key
            );
            if (!$result['result']) {
                throw new RuntimeException($result['message']);
            }

            $this->orderService->updateOrders($botDto, $user->id);

            $result = OrderResource::collection(Order::query()->where(['user_id' => $user->id])->
            where(['bot_id' => $bot->id])->get());

            return ApiHelpers::success($result);
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (Throwable $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Orders error');
        }
    }


    /**
     * Получение активного заказа
     *
     * @param Request $request
     * @return array|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrder(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
            if (!is_numeric($request->user_id))
                return ApiHelpers::error('Invalid params: user_id');
            $user = User::query()->where(['telegram_id' => $request->user_id])->first();
            if (is_null($request->order_id))
                return ApiHelpers::error('Not found params: order_id');
            /** @var Order|null $order */
            $order = Order::query()->where(['order_id' => $request->order_id])->first();
            if (!$order instanceof Order)
                return ApiHelpers::error('Not found order.');
            if (is_null($request->user_secret_key))
                return ApiHelpers::error('Not found params: user_secret_key');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);
            if ($botDto->version != 3) {
                BotLogHelpers::notifyBotLog('(🟣KEY не поменял ' . $botDto->bot_id);
                return ApiHelpers::error('Fatal Error');
            }
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

            return ApiHelpers::success(OrderResource::generateOrderArray($order));
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (Throwable $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Get order error');
        }
    }
}
