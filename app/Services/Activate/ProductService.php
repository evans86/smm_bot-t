<?php

namespace App\Services\Activate;

use App\Services\External\ActivApi;
use App\Services\MainService;

class ProductService extends MainService
{
    /**
     * Все доступные сервисы с API
     *
     * @param $country
     * @return array
     */
    public function getAllProducts($country = null)
    {
        //оставить свой API
        $smsActivate = new ActivApi(config('services.key_activate.key'), BotService::DEFAULT_HOST);

        return $smsActivate->getNumbersStatus($country);
    }

    /**
     * Сервисы доступные для конкретной страны
     *
     * @return array
     */
    public function getPricesCountry($bot)
    {
        $smsActivate = new ActivApi($bot->api_key, $bot->resource_link);

        if($bot->resource_link == BotService::DEFAULT_HOST) {
            $services = $smsActivate->getTopCountriesByService();
            return $this->formingPricesArr($services);
        } else {
            $services = $smsActivate->getPrices();
            return $this->formingPricesArr($services);
        }
    }

    /**
     * @param $services
     * @return array
     */
    private function formingPricesArr($services)
    {
        $result = [];
        foreach ($services as $key => $service) {

            array_push($result, [
                'name' => $key,
                'image' => 'https://smsactivate.s3.eu-central-1.amazonaws.com/assets/ico/' . $key . '0.webp',
            ]);
        }

        return $result;
    }
}
