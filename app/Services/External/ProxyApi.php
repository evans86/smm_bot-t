<?php

namespace App\Services\External;

use GuzzleHttp\Client;

class ProxyApi
{
    const HOST = 'https://proxy6.net/api/';

    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    //Проверка соединения
    public function ping()
    {
        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey);

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Получение информации о сумме заказа;
    public function getprice($count, $period, $version = 6)
    {
        $requestParam = [
            'count' => $count,
            'period' => $period,
            'version' => $version,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));
//        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam), [
//            'proxy' => 'http://VtZNR9Hb:nXC9nQ45@86.62.52.85:62959'
//        ]);

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Получение информации о доступном кол-ве прокси для конкретной страны;
    public function getcount($country, $version = 6)
    {
        $requestParam = [
            'country' => $country,
            'version' => $version,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Получение списка доступных стран;
    public function getcountry($version = 6)
    {
        $requestParam = [
            'version' => $version,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Получение списка ваших прокси;
    public function getproxy($descr, $state = 'all', $page = 1, $limit = 1000)
    {
        $requestParam = [
            'state' => $state,
            'descr' => $descr,
            'limit' => $limit,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Изменение типа (протокола) прокси;
    public function settype($ids, $type = 'http')
    {
        $requestParam = [
            'ids' => $ids,
            'type' => $type
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Обновление технического комментария;
    public function setdescr($new, $old, $ids)
    {
        $requestParam = [
            'new' => $new,
            'old' => $old,
            'ids' => $ids,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Покупка прокси;
    public function buy($count, $period, $country, $version = 6, $type = 'http', $descr =null)
    {
        $requestParam = [
            'count' => $count,
            'period' => $period,
            'country' => $country,
            'descr' => $descr,
            'version' => $version,
            'type' => $type,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Продление списка прокси;
    public function prolong($period, $ids)
    {
        $requestParam = [
            'period' => $period,
            'ids' => $ids,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Удаление прокси;
    public function delete($ids, $descr = null)
    {
        $requestParam = [
            'ids' => $ids,
            'descr' => $descr,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Проверка валидности прокси.
    public function check($ids)
    {
        $requestParam = [
            'ids' => $ids,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    //Привязка/удаление авторизации прокси по ip.
    public function ipauth($ip)
    {
        $requestParam = [
            'ip' => $ip,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get($this->apiKey . '/' . __FUNCTION__ . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

}
