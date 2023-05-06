<?php

namespace App\Services\Activate;

use App\Services\External\ProxyApi;
use App\Services\MainService;

class ProxyService extends MainService
{
    /**
     * @param $country
     * @param $version
     * @return mixed
     */
    public function getCount($country, $version)
    {
        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $count = $proxyApi->getcount($country, $version);

        return $count['count'];
    }

    /**
     * @param $count
     * @param $period
     * @param $version
     * @return array
     */
    public function getPrice($count, $period, $version)
    {
        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $price = $proxyApi->getprice($count, $period, $version);

        $result = [];

        array_push($result, [
            'price' => $price['price'],
            'period' => $price['period'],
            'count' => $price['count'],
            'price_single' => $price['price_single'],
        ]);

        return $result;
    }
}
