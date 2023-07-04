<?php

namespace App\Services\External;

use GuzzleHttp\Client;

class PartnerApi
{
    const HOST = 'https://partner.soc-proof.su/api/';

    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function services()
    {
        $requestParam = [
            'key' => $this->apiKey,
            'action' => __FUNCTION__,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->post('v2' . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    public function status($order)
    {
        $requestParam = [
            'key' => $this->apiKey,
            'action' => __FUNCTION__,
            'order' => $order
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->post('v2' . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

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
        $expiry = null // Дата истечения срока действия. Формат d/m/Y
    )
    {
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
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->post('v2' . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }
}
