<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Models\Country\Description;
use App\Models\Social\Social;
use App\Services\External\PartnerApi;
use App\Services\MainService;
use DiDom\Document;
use GuzzleHttp\Client;

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

        foreach ($socials as $key => $social) {

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
//        echo '<pre>';
//        var_dump($services);
//        echo '</pre>';
//        dd($services[500]);
        $social = Social::query()->where(['id' => $social])->first();

        $result = [];

        foreach ($services as $key => $service) {
            switch ($service['type']) {
                case 'Package':
                case 'Subscriptions ':
                case 'Custom Comments':
                case 'Mentions User Followers':
                case 'Custom Comments Package':
                    break;
                case 'Default':
                case 'Poll':
                    if (str_contains($service['category'], $social->name_en)) {

                        array_push($result, [
                            'name_category' => $service['category'],
                        ]);
                    }
            }
        }

        $result = array_unique($result, SORT_REGULAR);

//        dd($result);

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

        //отработать black и white list

        foreach ($services as $key => $service) {
            switch ($service['type']) {
                case 'Package':
                case 'Subscriptions ':
                case 'Custom Comments':
                case 'Mentions User Followers':
                case 'Custom Comments Package':
                    break;
                case 'Default':
                case 'Poll':
                    if (($service['category'] == $name_category)) {

                        $description = Description::query()->where(['type_id' => $service['service']])->first();
                        $amountStart = (int)ceil(floatval($service['rate']) * 100);
                        $amountFinal = $amountStart + $amountStart * $botDto->percent / 100;

                        array_push($result, [
                            'type_id' => $service['service'],//ид типа товара
                            'name' => $service['name'],//название товара
                            'min' => $service['min'],//минимаьлное количество товара
                            'max' => $service['max'],//максимально возможное количество единиц товара
                            'rate' => $amountFinal,//цена за 1000 единиц (посчитать с наценкой)
                            'type' => $service['type'],//с каким типом дальше создавать заказ
                            'desc_ru' => $description->desc_ru,
                            'desc_eng' => $description->desc_eng,
                        ]);
                    }
            }

        }

//        dd($result);

        return $result;
    }

    /**
     * Крон для обновления описания
     *
     * @return void
     * @throws \DiDom\Exceptions\InvalidSelectorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cronUpdateDescription()
    {
        $descriptions = $this->getDescription();

        echo 'Получен массив описаний' . PHP_EOL;

        foreach ($descriptions as $key => $description) {
            echo 'start to: ' . $key . PHP_EOL;

            $data = [
                'type_id' => $key,
                'desc_ru' => $description['desc_ru'],
                'desc_eng' => $description['desc_eng'],
            ];

            Description::updateOrCreate($data);

            echo 'finish to: ' . $key . PHP_EOL;
        }

        echo 'Массив описаний обновлен' . PHP_EOL;
    }

    /**
     * Получение описания с сайта
     *
     * @return array
     * @throws \DiDom\Exceptions\InvalidSelectorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDescription()
    {
        $client = new Client(['base_uri' => 'https://partner.soc-proof.su/']);
        $response = $client->request('GET', 'services');

        $content = $response->getBody()->getContents();
        $document = new Document($content);
        $modals = $document->find('.modal.fade');
        $results = [];
        foreach ($modals as $modal) {
            $id = substr($modal->getAttribute('id'), 6);
            $id = intval($id);
            $results[$id]['id'] = $id;
            $desc = $modal->first('.modal-body')->text();
            $results[$id]['desc_ru'] = $desc;
        }

        $client = new Client(['base_uri' => 'https://partner.soc-proof.su/']);
        $response = $client->request('GET', 'en/services');
        $content = $response->getBody()->getContents();

        $document = new Document($content);
        $modals = $document->find('.modal.fade');
        foreach ($modals as $modal) {
            $id = substr($modal->getAttribute('id'), 6);
            $id = intval($id);
            $desc = $modal->first('.modal-body')->text();
            $results[$id]['desc_eng'] = $desc;
        }
//        dd($results);

        return $results;
//        dd(json_encode($results));
    }
}
