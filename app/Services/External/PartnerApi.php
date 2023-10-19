<?php

namespace App\Services\External;

use App\Helpers\BotLogHelpers;
use GuzzleHttp\Client;

class PartnerApi
{
    const HOST = 'https://partner.soc-proof.su/api/';

    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Массив товаров (типов), содержит всю информацию
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function services()
    {
        try {
            $requestParam = [
                'key' => $this->apiKey,
                'action' => __FUNCTION__,
            ];

            $client = new Client(['base_uri' => self::HOST]);
            $response = $client->post('v2' . '?' . http_build_query($requestParam));

            $result = $response->getBody()->getContents();
            return json_decode($result, true);
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            throw new \RuntimeException('Ошибка в получении данных провайдера');
        }
    }

    /**
     * Статус заказа
     *
     * @param $order
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function status($order)
    {
        try {
            $requestParam = [
                'key' => $this->apiKey,
                'action' => __FUNCTION__,
                'order' => $order
            ];

            $client = new Client(['base_uri' => self::HOST]);
            $response = $client->post('v2' . '?' . http_build_query($requestParam));

            $result = $response->getBody()->getContents();
            return json_decode($result, true);
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            throw new \RuntimeException('Ошибка в получении данных провайдера');
        }
    }

    /**
     * Создание заказа (пока работает Default, Poll)
     *
     * @param $type_id
     * @param $link
     * @param $quantity
     * @param $comments
     * @param $username
     * @param $answer_number
     * @param $min
     * @param $max
     * @param $posts
     * @param $old_posts
     * @param $delay
     * @param $expiry
     * @param $runs
     * @param $interval
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(
        $type_id,
        $link = null, // Ссылка на страницу
        $quantity = null, // Необходимое количество
        $comments = null, // Список комментариев, разделенных символами \r\n или \n
        $username = null, // URL-адрес для удаления подписчиков с
        $answer_number = null, // Номер ответа в опросе
        $min = null, // Минимальное количество
        $max = null, // Максимальное количество
        $posts = null, // Ограничить количество новых (будущих) записей, которые будут проанализированы и для которых будут создаваться заказы
        $old_posts = null, // Количество существующих записей, которые будут проанализированы и для которых будут созданы заказы
        $delay = null, // Задержка в минутах. Возможные значения: 0, 5, 10, 15, 30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 360, 420, 480, 540, 600
        $expiry = null, // Дата истечения срока действия. Формат d/m/Y
        $runs = null,
        $interval = null
    )
    {
        try {
            $requestParam = [
                'key' => $this->apiKey,
                'action' => __FUNCTION__,

                'service' => $type_id,
                'link' => $link,
                'quantity' => $quantity,
                'comments' => $comments,
                'username' => $username,
                'answer_number' => $answer_number,
                'min' => $min,
                'max' => $max,
                'posts' => $posts,
                'old_posts' => $old_posts,
                'delay' => $delay,
                'expiry' => $expiry,
                'runs' => $runs,
                'interval' => $interval,
            ];

            $client = new Client(['base_uri' => self::HOST]);
            $response = $client->post('v2' . '?' . http_build_query($requestParam));

            $result = $response->getBody()->getContents();
            return json_decode($result, true);
        } catch (\RuntimeException $r) {
            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
            throw new \RuntimeException('Ошибка в получении данных провайдера');
        }
    }
}
