<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Models\Description\Description;
use App\Models\Social\Social;
use App\Services\External\PartnerApi;
use App\Services\MainService;
use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class SmmService extends MainService
{
    /**
     * Формирование массива соц.сетей
     *
     * @param $socials
     * @return array
     */
    public function formingSocialArray($socials, BotDto $botDto)
    {
        $partnerApi = new PartnerApi($botDto->getEncryptedApiKey());
        $services = $partnerApi->services();

        if (!is_null($botDto->white))
            $white_array = explode(',', $botDto->white);

        $result = [];

        foreach ($socials as $key => $social) {

            foreach ($services as $k => $service) {
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
                            if (!is_null($botDto->white)) {
                                if (in_array($service['service'], $white_array)) {
                                    array_push($result, [
                                        'id' => $social->id,
                                        'name_en' => $social->name_en,
                                        'name_ru' => $social->name_ru,
                                        'image' => $social->image,
                                    ]);
                                } else {
                                    break;
                                }
                            } else {
                                array_push($result, [
                                    'id' => $social->id,
                                    'name_en' => $social->name_en,
                                    'name_ru' => $social->name_ru,
                                    'image' => $social->image,
                                ]);
                            }
                        }
                }
            }

            $result = array_unique($result, SORT_REGULAR);
        }

        return $result;
    }

//    /**
//     * Формирование массива соц.сетей
//     *
//     * @param $socials
//     * @return array
//     */
//    public function formingSocialArrays($socials)
//    {
//        $result = [];
//
//        foreach ($socials as $key => $social) {
//            array_push($result, [
//                'id' => $social->id,
//                'name_en' => $social->name_en,
//                'name_ru' => $social->name_ru,
//                'image' => $social->image,
//            ]);
//        }
//
//        return $result;
//    }

    /**
     * формирование массива категорий
     *
     * @param BotDto $botDto
     * @param $social
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function formingCategoriesArray(BotDto $botDto, $social)
    {
        $partnerApi = new PartnerApi($botDto->getEncryptedApiKey());
        $services = $partnerApi->services();
        $social = Social::query()->where(['id' => $social])->first();

        if (!is_null($botDto->white))
            $white_array = explode(',', $botDto->white);

        $result = [];

        foreach ($services as $key => $service) {
//            dd($service);

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

                        if (!is_null($botDto->white)) {
                            if (in_array($service['service'], $white_array)) {
                                array_push($result, [
                                    'name_category' => $service['category'],
                                ]);
                            } else {
                                break;
                            }
                        } else {
                            array_push($result, [
                                'name_category' => $service['category'],
                            ]);
                        }
                    }
            }
        }

        $result = array_unique($result, SORT_REGULAR);

        return $result;
    }

//    /**
//     * формирование массива категорий
//     *
//     * @param BotDto $botDto
//     * @param $social
//     * @return array
//     * @throws \GuzzleHttp\Exception\GuzzleException
//     */
//    public function formingCategoriesArrays(BotDto $botDto, $social)
//    {
//        $partnerApi = new PartnerApi($botDto->api_key);
//        $services = $partnerApi->services();
//        $social = Social::query()->where(['id' => $social])->first();
//
//        $result = [];
//
//        foreach ($services as $key => $service) {
//
//            switch ($service['type']) {
//                case 'Package':
//                case 'Subscriptions ':
//                case 'Custom Comments':
//                case 'Mentions User Followers':
//                case 'Custom Comments Package':
//                    break;
//                case 'Default':
//                case 'Poll':
//                    if (str_contains($service['category'], $social->name_en)) {
//
//                        array_push($result, [
//                            'name_category' => $service['category'],
//                        ]);
//                    }
//            }
//        }
//
//        $result = array_unique($result, SORT_REGULAR);
//
//        return $result;
//    }

    /**
     * Формирование массива типа товаров
     *
     * @param BotDto $botDto
     * @param $name_category
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function formingTypesArray(BotDto $botDto, $name_category)
    {
        $partnerApi = new PartnerApi($botDto->getEncryptedApiKey());
        $services = $partnerApi->services();

        $result = [];

        if (!is_null($botDto->black))
            $black_array = explode(',', $botDto->black);

        if (!is_null($botDto->white))
            $white_array = explode(',', $botDto->white);
//        dd($white_array);
//        dd($services);

        foreach ($services as $key => $service) {

            if (!is_null($botDto->black)) {
                if (in_array($service['service'], $black_array))
                    continue;
            }

            if (!is_null($botDto->white)) {
//                dd($service);
                if (!in_array($service['service'], $white_array))
                    continue;
            }
//            dd($service);

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
        $modals = $document->find('.modal');
        $results = [];
        foreach ($modals as $modal) {
            $id = substr($modal->getAttribute('id'), 6);
            $id = intval($id);
            $results[$id]['id'] = $id;
            $desc = $modal->first('.modal-body')->html();
            $results[$id]['desc_ru'] = $desc;
        }

        $client = new Client(['base_uri' => 'https://partner.soc-proof.su/']);
        $response = $client->request('GET', 'en/services');
        $content = $response->getBody()->getContents();

        $document = new Document($content);
        $modals = $document->find('.modal');
        foreach ($modals as $modal) {
            $id = substr($modal->getAttribute('id'), 6);
            $id = intval($id);
            $desc = $modal->first('.modal-body')->html();
            $results[$id]['desc_eng'] = $desc;
        }

        return $results;
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
        try {
            $descriptions = $this->getDescription();

            echo 'Получен массив описаний' . PHP_EOL;

            $start_text = 'Smm: Получен массив описаний' . PHP_EOL;
            $this->notifyTelegram($start_text);

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

            $finish_text = 'Smm: Массив описаний обновлен' . PHP_EOL;
            $this->notifyTelegram($finish_text);

        } catch (\Exception $e) {
            $this->notifyTelegram('🔴' . $e->getMessage());
        }
    }

    public function notifyTelegram($text)
    {
        $client = new Client([
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Принудительно IPv4
            ],
            'timeout' => 10,
            'connect_timeout' => 5,
        ]);

        $ids = [6715142449]; // Список chat_id
        $bots = [
            config('services.bot_api_keys.cron_log_bot_1'), // Основной бот
            config('services.bot_api_keys.cron_log_bot_2')  // Резервный бот
        ];

        // Если текст пустой, заменяем его на заглушку (или оставляем пустым)
        $message = ($text === '') ? '[Empty message]' : $text;

        $lastError = null;

        foreach ($bots as $botToken) {
            try {
                foreach ($ids as $id) {
                    $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        RequestOptions::JSON => [
                            'chat_id' => $id,
                            'text' => $message,
                        ],
                    ]);
                }
                return true; // Успешно отправлено
            } catch (\Exception $e) {
                $lastError = $e;
                continue; // Пробуем следующего бота
            }
        }

        // Если все боты не сработали, логируем ошибку (или просто игнорируем)
        error_log("Telegram send failed: " . $lastError->getMessage());
        return false;
    }
}
