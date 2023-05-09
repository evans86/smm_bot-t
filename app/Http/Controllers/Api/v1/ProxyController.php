<?php

namespace App\Http\Controllers\Api\v1;

use App\Dto\BotFactory;
use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;
use App\Models\Bot\Bot;
use App\Models\User\User;
use App\Services\Activate\ProxyService;
use App\Services\External\BottApi;
use http\Exception\RuntimeException;
use Illuminate\Http\Request;

class ProxyController extends Controller
{
    public ProxyService $proxyService;

    public function __construct()
    {
        $this->proxyService = new ProxyService();
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public function getProxy(Request $request)
    {
        try {
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);

            $result = $this->proxyService->formingProxy($botDto);

            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCount(Request $request)
    {
        try {
            if (is_null($request->country))
                return ApiHelpers::error('Not found params: country');
            if (is_null($request->version))
                return ApiHelpers::error('Not found params: version');
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
//            if (is_null($request->user_secret_key))
//                return ApiHelpers::error('Not found params: user_secret_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

//            //позже передать
            $botDto = BotFactory::fromEntity($bot);
//            $result = BottApi::checkUser(
//                $request->user_id,
//                $request->user_secret_key,
//                $botDto->public_key,
//                $botDto->private_key
//            );
//            if (!$result['result']) {
//                throw new RuntimeException($result['message']);
//            }

            $result = $this->proxyService->getCount($request->country, $request->version, $botDto);

            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     *
     * @param Request $request
     * @return array|string
     */
    public function getPrice(Request $request)
    {
        try {
            if (is_null($request->count))
                return ApiHelpers::error('Not found params: count');
            if (is_null($request->version))
                return ApiHelpers::error('Not found params: version');
            if (is_null($request->period))
                return ApiHelpers::error('Not found params: period');
//            if (is_null($request->public_key))
//                return ApiHelpers::error('Not found params: public_key');
//            $bot = Bot::query()->where('public_key', $request->public_key)->first();
//            if (empty($bot))
//                return ApiHelpers::error('Not found module.');
//
//            //позже передать
//            $botDto = BotFactory::fromEntity($bot);

            $result = $this->proxyService->getPrice($request->count, $request->period, $request->version);

            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public function buyProxy(Request $request)
    {
        try {
            if (is_null($request->count))
                return ApiHelpers::error('Not found params: count');
            if (is_null($request->period))
                return ApiHelpers::error('Not found params: period');
            if (is_null($request->country))
                return ApiHelpers::error('Not found params: country');
            if (is_null($request->version))
                return ApiHelpers::error('Not found params: version');
            if (is_null($request->type))
                return ApiHelpers::error('Not found params: type');

//            if (is_null($request->public_key))
//                return ApiHelpers::error('Not found params: public_key');
//            $bot = Bot::query()->where('public_key', $request->public_key)->first();
//            if (empty($bot))
//                return ApiHelpers::error('Not found module.');
//            if (is_null($request->user_secret_key))
//                return ApiHelpers::error('Not found params: user_secret_key');
//              //передать потом
//            $botDto = BotFactory::fromEntity($bot);
//            $result = BottApi::checkUser(
//                $request->user_id,
//                $request->user_secret_key,
//                $botDto->public_key,
//                $botDto->private_key
//            );
//            if (!$result['result']) {
//                throw new RuntimeException($result['message']);
//            }
//            if ($result['data']['money'] == 0) {
//                throw new RuntimeException('Пополните баланс в боте');
//            }

            $result = $this->proxyService->createOrder(
                $request->count,
                $request->period,
                $request->country,
                $request->version,
                $request->type,
//                $result['data'],
//                $botDto
            );

            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public function getOrders(Request $request)
    {
        try {
            if (is_null($request->user_id))
                return ApiHelpers::error('Not found params: user_id');
//            $user = User::query()->where(['telegram_id' => $request->user_id])->first();

            //            if (is_null($request->public_key))
//                return ApiHelpers::error('Not found params: public_key');
//            $bot = Bot::query()->where('public_key', $request->public_key)->first();
//            if (empty($bot))
//                return ApiHelpers::error('Not found module.');
//            if (is_null($request->user_secret_key))
//                return ApiHelpers::error('Not found params: user_secret_key');
//
//            $botDto = BotFactory::fromEntity($bot);
//            $result = BottApi::checkUser(
//                $request->user_id,
//                $request->user_secret_key,
//                $botDto->public_key,
//                $botDto->private_key
//            );
//            if (!$result['result']) {
//                throw new RuntimeException($result['message']);
//            }
//            if ($result['data']['money'] == 0) {
//                throw new RuntimeException('Пополните баланс в боте');
//            }

            $result = $this->proxyService->getOrders(
                $request->user_id
//                $result['data'],
//                $botDto
            );
            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public function checkWork(Request $request)
    {
        try {
            if (is_null($request->order_org_id))
                return ApiHelpers::error('Not found params: order_org_id');
//        if (is_null($request->public_key))
//            return ApiHelpers::error('Not found params: public_key');
//        if (is_null($request->user_secret_key))
//            return ApiHelpers::error('Not found params: user_secret_key');
//            $bot = Bot::query()->where('public_key', $request->public_key)->first();
//            if (empty($bot))
//                return ApiHelpers::error('Not found module.');
//
//            //позже передать
//            $botDto = BotFactory::fromEntity($bot);
//            $result = BottApi::checkUser(
//                $request->user_id,
//                $request->user_secret_key,
//                $botDto->public_key,
//                $botDto->private_key
//            );
//            if (!$result['result']) {
//                throw new RuntimeException($result['message']);
//            }

            $result = $this->proxyService->checkWork(
                $request->order_org_id
//                $botDto
            );
            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public function updateType(Request $request)
    {
        try {
            if (is_null($request->order_org_id))
                return ApiHelpers::error('Not found params: order_org_id');
            if (is_null($request->type))
                return ApiHelpers::error('Not found params: type');
            //        if (is_null($request->public_key))
//            return ApiHelpers::error('Not found params: public_key');
//        if (is_null($request->user_secret_key))
//            return ApiHelpers::error('Not found params: user_secret_key');
//            $bot = Bot::query()->where('public_key', $request->public_key)->first();
//            if (empty($bot))
//                return ApiHelpers::error('Not found module.');
//
//            //позже передать
//            $botDto = BotFactory::fromEntity($bot);
//            $result = BottApi::checkUser(
//                $request->user_id,
//                $request->user_secret_key,
//                $botDto->public_key,
//                $botDto->private_key
//            );
//            if (!$result['result']) {
//                throw new RuntimeException($result['message']);
//            }

            $result = $this->proxyService->updateType(
                $request->order_org_id,
                $request->type
//                $result['data'],
//                $botDto
            );
            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    public function deleteProxy(Request $request)
    {
        try {
            if (is_null($request->order_org_id))
                return ApiHelpers::error('Not found params: order_org_id');
            //        if (is_null($request->public_key))
//            return ApiHelpers::error('Not found params: public_key');
//        if (is_null($request->user_secret_key))
//            return ApiHelpers::error('Not found params: user_secret_key');
//            $bot = Bot::query()->where('public_key', $request->public_key)->first();
//            if (empty($bot))
//                return ApiHelpers::error('Not found module.');
//
//            //позже передать
//            $botDto = BotFactory::fromEntity($bot);
//            $result = BottApi::checkUser(
//                $request->user_id,
//                $request->user_secret_key,
//                $botDto->public_key,
//                $botDto->private_key
//            );
//            if (!$result['result']) {
//                throw new RuntimeException($result['message']);
//            }

            $result = $this->proxyService->deleteProxy(
                $request->order_org_id,
//                $result['data'],
//                $botDto
            );

            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }
}
