<?php

namespace App\Services\Activate;

use App\Services\External\ProxyApi;
use App\Services\MainService;

class CountryService extends MainService
{
    /**
     * @param $version
     * @return array
     */
    public function formingCountriesArray($version)
    {
        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $countries = $proxyApi->getcountry($version);

        $countries = $countries['list'];
        $result = [];

        foreach ($countries as $key => $country){
            array_push($result, [
                'id' => $key,
                'title' => $country,
//                'image' => $smsCountry->image
            ]);
        }

        return $result;
    }
}
