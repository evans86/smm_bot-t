<?php

namespace App\Http\Controllers\Api\v1;

use App\Dto\BotFactory;
use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;
use App\Models\Bot\Bot;
use App\Models\Social\Social;
use App\Services\Activate\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * @var CountryService
     */
    public CountryService $countryService;

    public function __construct()
    {
        $this->countryService = new CountryService();
    }

    /**
     * @return array
     */
    public function getSocial()
    {
        try {
            $socials = Social::all();

            $result = $this->countryService->formingSocialArray($socials);

            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
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

            $result = $this->countryService->formingCategoriesArray($botDto, $request->social);

            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }

    /**
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

            $result = $this->countryService->formingTypesArray($botDto, $request->name_category);

            return ApiHelpers::success($result);
        } catch (\RuntimeException $e) {
            return ApiHelpers::errorNew($e->getMessage());
        }
    }
}
