<?php

namespace App\Services\Activate;

use App\Models\Country\Country;
use App\Models\Proxy\Proxy;
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

    public function formingProxy()
    {
        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $proxies = Proxy::all();

        $result = [];
        foreach ($proxies as $key => $proxy) {

            $countries = $proxyApi->getcountry($proxy->version);
            $countries = $countries['list'];

            $countriesArr = [];
            foreach ($countries as $country) {

                try {
                    $countryProxy = Country::query()->where(['iso_two' => $country])->first();

                    array_push($countriesArr, [
                        'org_id' => $countryProxy->iso_two,
                        'name_ru' => $countryProxy->name_ru,
                        'name_en' => $countryProxy->name_en,
                        'image' => $countryProxy->image
                    ]);
                } catch (\Exception $e) {
                    continue;
                }
            }

            array_push($result, [
                'title' => $proxy->title,
                'version' => $proxy->version,
                'countries' => $countriesArr
            ]);
        }

        return $result;
    }
}
