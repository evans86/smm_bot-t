<?php

namespace App\Http\Controllers\Api\v1;

use App\Dto\BotFactory;
use App\Helpers\ApiHelpers;
use App\Helpers\BotLogHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bot\BotCreateRequest;
use App\Http\Requests\Bot\BotGetRequest;
use App\Http\Requests\Bot\BotRotatePrivateKeyRequest;
use App\Http\Requests\Bot\BotUpdateRequest;
use App\Models\Bot\Bot;
use App\Services\Activate\BotService;
use Illuminate\Http\Request;

class BotController extends Controller
{
    private BotService $botService;

    public function __construct()
    {
        $this->middleware('api');
        $this->botService = new BotService();
    }

    /**
     * Запрос проверки доступности сервиса
     *
     * @return array
     */
    public function ping()
    {
        return ApiHelpers::successStr('OK');
    }

    /**
     * Запрос создания веб–модуля
     * добавить возврат ключа в виде
     *
     * @param BotCreateRequest $request
     * @return array|string
     */
    public function create(BotCreateRequest $request)
    {
        try {
            $bot = $this->botService->create(
                $request->public_key,
                $request->private_key,
                $request->bot_id
            );
            return ApiHelpers::success(BotFactory::fromEntity($bot)->getArray());
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (\Exception $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Module creation error');
        }
    }

    /**
     * Получение актуальных настроек
     * добавить возврат ключа в виде
     *
     * @param BotGetRequest $request
     * @return array|string
     */
    public function get(BotGetRequest $request)
    {
        try {
            $bot = Bot::query()
                ->where('public_key', $request->public_key)
                ->where('private_key', $request->private_key)
                ->first();

            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            return ApiHelpers::success(BotFactory::fromEntity($bot)->getArray());
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (\Exception $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Module get error');
        }
    }

    /**
     * Обновление настроек в модуле
     *
     * @param BotUpdateRequest $request
     * @return array|string
     */
    public function update(BotUpdateRequest $request)
    {
        try {
            $bot = $this->botService->update($request->getDto());

//            $bot = Bot::query()
//                ->where('public_key', $bot->public_key)
//                ->where('private_key', $bot->private_key)
//                ->first();

            return ApiHelpers::success(BotFactory::fromEntity($bot)->getArray());
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (\Exception $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Module update error');
        }
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public function getSettings(Request $request)
    {
        try {
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                throw new \RuntimeException('Not found module.');

            if ($bot->is_saved) {
                return ApiHelpers::success(BotFactory::fromEntity($bot)->getSettings());
            } else {
                $botDto = BotFactory::fromEntity($bot);

                $result = [
                    'color' => $botDto->color,
                    'black' => $botDto->black,
                    'white' => $botDto->white,
                ];
                return ApiHelpers::success($result);
            }
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (\Exception $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Module get settings error');
        }
    }

    /**
     * Удаление модуля
     *
     * @param Request $request
     * @return array|string
     */
    public function delete(Request $request)
    {
        try {
            $this->botService->delete($request->public_key, $request->private_key);
            return ApiHelpers::success('OK');
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (\Exception $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Module delete error');
        }
    }

    /**
     * Смена private_key модуля (вызывается из Bot-t при ротации секрета).
     */
    public function rotatePrivateKey(BotRotatePrivateKeyRequest $request)
    {
        try {
            $bot = $this->botService->rotatePrivateKey(
                $request->public_key,
                $request->private_key,
                $request->new_private_key
            );

            return ApiHelpers::success(BotFactory::fromEntity($bot)->getArray());
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error($r->getMessage());
        } catch (\Exception $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Module rotate private key error');
        }
    }
}
