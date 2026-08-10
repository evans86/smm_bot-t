<?php

namespace App\Services\External;

use App\Dto\BotDto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class BottApi
{
    const HOST = 'https://api.bot-t.com/';

    /**
     * Выполняет запросы к Bot-t без утечки query/form параметров в тексты ошибок Guzzle.
     *
     * @throws RuntimeException
     */
    private static function requestJson(Client $client, string $method, string $uri, array $options = []): array
    {
        $options['http_errors'] = false;

        try {
            $response = $client->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            throw new RuntimeException('Bott API request failed');
        }

        $contents = $response->getBody()->getContents();
        $result = json_decode($contents, true);

        if (is_array($result)) {
            return $result;
        }

        return [
            'result' => false,
            'message' => 'Bott API invalid response',
            'data' => [],
        ];
    }

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
        return self::requestJson($client, 'GET', 'v1/module/user/check-secret', [
            'query' => $requestParam,
        ]);
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
        return self::requestJson($client, 'GET', 'v1/module/user/get', [
            'query' => $requestParam,
        ]);
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
        return self::requestJson($client, 'POST', 'subtract-balance', [
            'form_params' => $requestParam,
            'headers' => [
                'User-Agent' => $comment,
            ]
        ]);
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
        return self::requestJson($client, 'POST', 'add-balance', [
            'form_params' => $requestParam,
            'headers' => [
                'User-Agent' => $comment,
            ]
        ]);
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
        return self::requestJson($client, 'POST', 'order-create', [
            'form_params' => $requestParam,
            'headers' => [
                'User-Agent' => $product,
            ]
        ]);
    }
}
