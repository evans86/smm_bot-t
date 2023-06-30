<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Models\Social\Social;
use App\Services\External\PartnerApi;
use App\Services\External\ProxyApi;
use App\Services\MainService;

class CountryService extends MainService
{
    /**
     * Формирование массива соц.сетей
     *
     * @param $socials
     * @return array
     */
    public function formingSocialArray($socials)
    {
        $result = [];

        foreach ($socials as $key => $social){

            array_push($result, [
                'id' => $social->id,
                'name_en' => $social->name_en,
                'name_ru' => $social->name_ru,
                'image' => $social->image,
            ]);
        }

        return $result;
    }

    /**
     * формирование массива категорий
     *
     * @param BotDto $botDto
     * @param $social
     * @return array
     */
    public function formingCategoriesArray(BotDto $botDto, $social)
    {
        $partnerApi = new PartnerApi($botDto->api_key);
        $services = $partnerApi->services();
        $social = Social::query()->where(['id' => $social])->first();

        $result = [];

        foreach ($services as $key => $service){
            if (str_contains($service['category'], $social->name_en)){

                array_push($result, [
                    'name_category' => $service['category'],
                ]);
            }
        }

        $result = array_unique($result, SORT_REGULAR);

        return $result;
    }

    /**
     * Формирование массива типа товаров
     *
     * @param BotDto $botDto
     * @param $name_category
     * @return array
     */
    public function formingTypesArray(BotDto $botDto, $name_category)
    {
        $partnerApi = new PartnerApi($botDto->api_key);
        $services = $partnerApi->services();

        $result = [];

        foreach ($services as $key => $service){
            if (($service['category'] == $name_category)){

                array_push($result, [
                    'type_id' => $service['service'],//ид типа товара
                    'name' => $service['name'],//название товара
                    'min' => $service['min'],//минимаьлное количество товара
                    'max' => $service['max'],//максимально возможное количество единиц товара
                    'rate' => $service['rate'],//цена за 1000 единиц
                    'type' => $service['type'],//с каким типом дальше создавать заказ
                ]);
            }
        }

        return $result;
    }
}
