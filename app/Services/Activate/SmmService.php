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
        $client = new Client(['base_uri' => 'https://soc-proof.su/']);
        $response = $client->request('GET', 'services');

        $content = $response->getBody()->getContents();
        $results = [];
        $results = $this->mergeDescriptionResults(
            $results,
            $this->parseModalDescriptions($content, 'desc_ru')
        );

        $client = new Client(['base_uri' => 'https://soc-proof.su/']);
        $response = $client->request('GET', 'en/services');
        $content = $response->getBody()->getContents();

        $results = $this->mergeDescriptionResults(
            $results,
            $this->parseModalDescriptions($content, 'desc_eng')
        );

        if (count($results) > 0) {
            return $results;
        }

        $response = $client->request('GET', 'services');
        $ruContent = $response->getBody()->getContents();
        $results = $this->mergeDescriptionResults(
            $results,
            $this->parseTableDescriptions($ruContent, 'desc_ru', 'ru')
        );

        $response = $client->request('GET', 'en/services');
        $enContent = $response->getBody()->getContents();
        return $this->mergeDescriptionResults(
            $results,
            $this->parseTableDescriptions($enContent, 'desc_eng', 'eng')
        );
    }

    private function parseModalDescriptions(string $content, string $field): array
    {
        $document = new Document($content);
        $results = [];

        foreach ($document->find('.modal') as $modal) {
            $id = intval(substr((string)$modal->getAttribute('id'), 6));
            $body = $modal->first('.modal-body');

            if ($id <= 0 || $body === null) {
                continue;
            }

            $results[$id] = [
                'id' => $id,
                $field => $body->html(),
            ];
        }

        return $results;
    }

    private function parseTableDescriptions(string $content, string $field, string $language): array
    {
        $document = new Document($content);
        $results = [];

        foreach ($document->find('table tr') as $row) {
            $cells = $row->find('td');

            if (count($cells) < 6) {
                continue;
            }

            $idText = trim($cells[0]->text());
            if (!preg_match('/^\d+$/', $idText)) {
                continue;
            }

            $id = intval($idText);
            if ($id <= 0) {
                continue;
            }

            $results[$id] = [
                'id' => $id,
                $field => $this->buildTableDescriptionHtml(
                    $cells[1]->text(),
                    $cells[2]->text(),
                    $cells[3]->text(),
                    $cells[4]->text(),
                    $cells[5]->text(),
                    $language
                ),
            ];
        }

        return $results;
    }

    private function mergeDescriptionResults(array $base, array $incoming): array
    {
        foreach ($incoming as $id => $description) {
            $base[$id] = array_merge($base[$id] ?? ['id' => $id], $description);
        }

        return $base;
    }

    private function buildTableDescriptionHtml(
        string $name,
        string $rate,
        string $min,
        string $max,
        string $averageTime,
        string $language
    ): string {
        $labels = $language === 'ru'
            ? [
                'service' => 'Услуга',
                'rate' => 'Цена за 1000',
                'min' => 'Минимальный заказ',
                'max' => 'Максимальный заказ',
                'average_time' => 'Среднее время',
            ]
            : [
                'service' => 'Service',
                'rate' => 'Rate per 1000',
                'min' => 'Min order',
                'max' => 'Max order',
                'average_time' => 'Average time',
            ];

        $items = [
            $labels['service'] => $name,
            $labels['rate'] => $rate,
            $labels['min'] => $min,
            $labels['max'] => $max,
            $labels['average_time'] => $averageTime,
        ];

        $html = '<ul>';
        foreach ($items as $label => $value) {
            $value = trim($value);
            if ($value === '') {
                continue;
            }

            $html .= '<li><strong>' . $this->escapeDescriptionValue($label) . ':</strong> '
                . $this->escapeDescriptionValue($value) . '</li>';
        }

        return $html . '</ul>';
    }

    private function escapeDescriptionValue(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
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

            echo 'Получен массив описаний: ' . count($descriptions) . PHP_EOL;

            if (count($descriptions) === 0) {
                throw new \RuntimeException('Не найдены описания на странице soc-proof.su/services');
            }

            $start_text = 'Smm: Получен массив описаний' . PHP_EOL;
            $this->notifyTelegram($start_text);

            foreach ($descriptions as $key => $description) {
                echo 'start to: ' . $key . PHP_EOL;

                Description::updateOrCreate(
                    ['type_id' => $key],
                    [
                        'desc_ru' => $description['desc_ru'] ?? null,
                        'desc_eng' => $description['desc_eng'] ?? null,
                    ]
                );

                echo 'finish to: ' . $key . PHP_EOL;
            }

            echo 'Массив описаний обновлен' . PHP_EOL;

            $finish_text = 'Smm: Массив описаний обновлен' . PHP_EOL;
            $this->notifyTelegram($finish_text);

        } catch (\Exception $e) {
            echo 'Ошибка обновления описаний: ' . $this->sanitizeTelegramError($e->getMessage()) . PHP_EOL;
            $this->notifyTelegram('🔴' . $e->getMessage());
        }
    }

    private function sanitizeTelegramError(string $message): string
    {
        return preg_replace('/bot\d+:[A-Za-z0-9_-]+/i', 'bot[redacted]', $message) ?? $message;
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
        $message = $lastError ? $this->sanitizeTelegramError($lastError->getMessage()) : 'unknown error';
        error_log("Telegram send failed: " . $message);
        return false;
    }
}
