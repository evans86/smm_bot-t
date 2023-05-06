<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ApiHelpers;
use App\Http\Controllers\Controller;
use App\Services\Activate\ProxyService;
use Illuminate\Http\Request;

class ProxyController extends Controller
{
    public ProxyService $proxyService;

    public function __construct()
    {
        $this->proxyService = new ProxyService();
    }

    /**
     * @return array
     */
    public function getProxy()
    {
        $data = [
            [
                'version' => '3',
                'title' => 'IPv4 Shared'
            ],
            [
                'version' => '4',
                'title' => 'IPv4'
            ],
            [
                'version' => '6',
                'title' => 'IPv6'
            ]
        ];

        return ApiHelpers::success($data);
    }

    /**
     * @param Request $request
     * @return array|string
     */
    public function getCount(Request $request)
    {
        if (is_null($request->country))
            return ApiHelpers::error('Not found params: country');
        if (is_null($request->version))
            return ApiHelpers::error('Not found params: version');
//        if (is_null($request->public_key))
//            return ApiHelpers::error('Not found params: public_key');
//        if (is_null($request->user_secret_key))
//            return ApiHelpers::error('Not found params: user_secret_key');

        $result = $this->proxyService->getCount($request->country, $request->version);

        return ApiHelpers::success($result);
    }

    /**
     *
     * @param Request $request
     * @return array|string
     */
    public function getPrice(Request $request)
    {
        if (is_null($request->count))
            return ApiHelpers::error('Not found params: count');
        if (is_null($request->version))
            return ApiHelpers::error('Not found params: version');
        if (is_null($request->period))
            return ApiHelpers::error('Not found params: period');
//        if (is_null($request->public_key))
//            return ApiHelpers::error('Not found params: public_key');
//        if (is_null($request->user_secret_key))
//            return ApiHelpers::error('Not found params: user_secret_key');

        $result = $this->proxyService->getPrice($request->count, $request->period, $request->version);

        return ApiHelpers::success($result);
    }
}
