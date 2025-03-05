<?php

namespace App\Services\External;

use App\Dto\BotDto;
use GuzzleHttp\Client;

class BottApi
{
    const HOST = 'https://api.bot-t.com/';

    /**
     * Проверка $secret_key
     *
     * @param int $telegram_id
     * @param string $secret_key
     * @param string $public_key
     * @param string $private_key
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function checkUser(int $telegram_id, string $secret_key, string $public_key, string $private_key)
    {
        $requestParam = [
            'public_key' => $public_key,
            'private_key' => $private_key,
            'id' => $telegram_id,
            'secret_key' => $secret_key,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get('v1/module/user/check-secret?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    /**
     * Получение $secret_key
     *
     * @param int $telegram_id
     * @param string $public_key
     * @param string $private_key
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function get(int $telegram_id, string $public_key, string $private_key): array
    {
        $requestParam = [
            'public_key' => $public_key,
            'private_key' => $private_key,
            'id' => $telegram_id,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->get('v1/module/user/get?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    /**
     * Списание баланса
     *
     * @param BotDto $botDto
     * @param array $userData
     * @param int $amount
     * @param string $comment
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function subtractBalance(BotDto $botDto, array $userData, int $amount, string $comment)
    {
        $link = 'https://api.bot-t.com/v1/module/user/';
        $public_key = $botDto->public_key;
        $private_key = $botDto->private_key;
        $user_id = $userData['user']['telegram_id'];
        $secret_key = $userData['secret_user_key'];

        $requestParam = [
            'public_key' => $public_key,
            'private_key' => $private_key,
            'user_id' => $user_id,
            'secret_key' => $secret_key,
            'amount' => $amount,
            'comment' => $comment,
        ];

        $client = new Client(['base_uri' => $link]);
        $response = $client->request('POST', 'subtract-balance', [
            'form_params' => $requestParam,
            'headers' => [
                'User-Agent' => $comment,
            ]
        ]);

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    /**
     * Пополнение баланса
     *
     * @param BotDto $botDto
     * @param array $userData
     * @param int $amount
     * @param string $comment
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function addBalance(BotDto $botDto, array $userData, int $amount, string $comment)
    {
        $link = 'https://api.bot-t.com/v1/module/user/';
        $public_key = $botDto->public_key;
        $private_key = $botDto->private_key;
        $user_id = $userData['user']['telegram_id'];
        $secret_key = $userData['secret_user_key'];

        $requestParam = [
            'public_key' => $public_key,
            'private_key' => $private_key,
            'user_id' => $user_id,
            'secret_key' => $secret_key,
            'amount' => $amount,
            'comment' => $comment,
        ];

        $client = new Client(['base_uri' => $link]);
        $response = $client->request('POST', 'add-balance', [
            'form_params' => $requestParam,
            'headers' => [
                'User-Agent' => $comment,
            ]
        ]);

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }

    public static function createOrder(BotDto $botDto, array $userData, int $amount, string $product)
    {
        $link = 'https://api.bot-t.com/v1/module/shop/';
        $public_key = $botDto->public_key;
        $private_key = $botDto->private_key;
        $user_id = $userData['user']['telegram_id'];
        $secret_key = $userData['secret_user_key'];
        $category_id = $botDto->category_id;

        $requestParam = [
            'public_key' => $public_key,
            'private_key' => $private_key,
            'user_id' => $user_id,
            'secret_key' => $secret_key,
            'amount' => $amount,
            'count' => 1,
            'category_id' => $category_id,
            'product' => $product,
        ];

        $client = new Client(['base_uri' => $link]);
        $response = $client->request('POST', 'order-create', [
            'form_params' => $requestParam,
            'headers' => [
                'User-Agent' => $product,
            ]
        ]);

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }
}
