<?php

namespace App\Http\Controllers\Api\v1;

use App\Dto\BotFactory;
use App\Helpers\ApiHelpers;
use App\Helpers\BotLogHelpers;
use App\Http\Controllers\Controller;
use App\Models\Bot\Bot;
use App\Models\Social\Social;
use App\Services\Activate\SmmService;
use Illuminate\Http\Request;

class SmmController extends Controller
{
    /**
     * @var SmmService
     */
    public SmmService $smmService;

    public function __construct()
    {
        $this->smmService = new SmmService();
    }

    /**
     * Список социальных сетей
     *
     * @return array|string
     */
    public function getSocial(Request $request)
    {
        try {
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

            $socials = \Cache::get('social');
            if($socials === null){
                $socials = Social::all();
                \Cache::put('social', $socials, 900);
            }

            $result = $this->smmService->formingSocialArray(
                $socials,
                $botDto
            );

//            $result = $this->smmService->formingSocialArrays(
//                $socials
//            );

            return ApiHelpers::success($result);
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error('Ошибка получения данных провайдера');
        } catch (\Throwable $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Get Social error');
        }
    }

    /**
     * Список доступных категорий
     *
     * @param Request $request
     * @return array|string
     */
    public function getCategories(Request $request)
    {
        try {
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            if (is_null($request->social))
                return ApiHelpers::error('Not found params: social');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);
            if ($botDto->version != 3) {
                BotLogHelpers::notifyBotLog('(🟣KEY не поменял ' . $botDto->bot_id);
                return ApiHelpers::error('Fatal Error');
            }

            $result = $this->smmService->formingCategoriesArray($botDto, $request->social);

//            $result = $this->smmService->formingCategoriesArrays($botDto, $request->social);

            return ApiHelpers::success($result);
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error('Ошибка получения данных провайдера');
        } catch (\Throwable $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Get categories error');
        }
    }

    /**
     * Список доступных товаров (типов)
     *
     * @param Request $request
     * @return array|string
     */
    public function getTypes(Request $request)
    {
        try {
            if (is_null($request->public_key))
                return ApiHelpers::error('Not found params: public_key');
            if (is_null($request->name_category))
                return ApiHelpers::error('Not found params: name_category');
            $bot = Bot::query()->where('public_key', $request->public_key)->first();
            if (empty($bot))
                return ApiHelpers::error('Not found module.');

            $botDto = BotFactory::fromEntity($bot);
            if ($botDto->version != 3) {
                BotLogHelpers::notifyBotLog('(🟣KEY не поменял ' . $botDto->bot_id);
                return ApiHelpers::error('Fatal Error');
            }

            $result = $this->smmService->formingTypesArray(
                $botDto,
                $request->name_category
            );

            return ApiHelpers::success($result);
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            return ApiHelpers::error('Ошибка получения данных провайдера');
        } catch (\Throwable $e) {
            BotLogHelpers::notifyBotLog('(🟣E ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            \Log::error($e->getMessage());
            return ApiHelpers::error('Get Types error');
        }
    }
}
