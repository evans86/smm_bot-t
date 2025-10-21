<?php

namespace App\Services\External;

use App\Helpers\BotLogHelpers;
use App\Models\Bot;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Log;
use RuntimeException;

class PartnerApi
{
    const HOST = 'https://soc-proof.su/api/';
    const HMAC_ALGORITHM = 'sha256';

    private string $realApiKey;
    private string $clientIp;
    private string $userAgent;

    public function __construct(string $encryptedApiKey)
    {
        // Дешифровка ключа (если он зашифрован)
        $this->realApiKey = $this->decryptApiKey($encryptedApiKey);

        // Получаем данные о клиенте
        $this->clientIp = request()->ip();
        $this->userAgent = request()->userAgent() ?? 'unknown';

        // Логируем инициализацию
        $this->logAccess();
    }

    /**
     * Автоматически определяет, нужно ли дешифровать ключ
     */
    private function decryptApiKey(string $key): string
    {
        // Если ключ не зашифрован (не начинается с префикса шифрования Laravel)
//        if (!str_starts_with($key, 'eyJpdiI6')) {
//            return $key;
//        }

        try {
            return Crypt::decryptString($key);
        } catch (\Exception $e) {
            BotLogHelpers::notifyBotLog("API key decryption failed for IP: {$this->clientIp}");
            throw new RuntimeException('Invalid API key format');
        }
    }

    /**
     * Логирование доступа к API
     */
    private function logAccess(): void
    {
//        BotLogHelpers::notifyBotLog("API access from IP: {$this->clientIp}, User-Agent: {$this->userAgent}");

        Log::channel('api_audit')->info('API initialized', [
            'ip' => $this->clientIp,
            'user_agent' => $this->userAgent,
            'key_fragment' => substr($this->realApiKey, -4)
        ]);
    }

    /**
     * Генерация HMAC подписи
     */
    private function generateHmac(array $data): string
    {
        ksort($data);
        $queryString = http_build_query($data);
        return hash_hmac(self::HMAC_ALGORITHM, $queryString, $this->realApiKey);
    }

    /**
     * Базовый метод запроса
     */
    private function makeRequest(string $action, array $params = []): array
    {
        $requestParams = array_merge($params, [
            'key' => $this->realApiKey,
            'action' => $action,
            'timestamp' => time(),
        ]);

//        BotLogHelpers::notifyBotLog('ЧТО с КЛЮЧОМ: ' . $this->realApiKey);

        // Добавляем HMAC подпись
        $requestParams['hmac'] = $this->generateHmac($requestParams);

        $client = new Client([
            'base_uri' => self::HOST,
            'timeout' => 15,
            'headers' => [
                'X-Client-IP' => $this->clientIp,
                'X-User-Agent' => $this->userAgent,
            ]
        ]);

        try {
            $response = $client->post('v2', ['form_params' => $requestParams]);
            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['error'])) {
                throw new RuntimeException($result['error']);
            }

            return $result;
        } catch (GuzzleException $e) {
//            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $e->getMessage());
            $this->logError($action, $e);
            throw new RuntimeException('API request failed');
        }
    }

    /**
     * Логирование ошибок
     */
    private function logError(string $action, \Throwable $e): void
    {
        \Log::channel('api_errors')->error("API $action failed", [
            'ip' => $this->clientIp,
            'error' => $e->getMessage(),
            'key_fragment' => substr($this->realApiKey, -4)
        ]);
    }

    public function services(): array
    {
        return $this->makeRequest('services');
    }

    public function status($order): array
    {
        return $this->makeRequest('status', ['order' => $order]);
    }

    public function add(
        $type_id,
        $link = null,
        $quantity = null,
        $comments = null,
        $username = null,
        $answer_number = null,
        $min = null,
        $max = null,
        $posts = null,
        $old_posts = null,
        $delay = null,
        $expiry = null,
        $runs = null,
        $interval = null
    ): array {
        $params = [
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

        // Дополнительное логирование для создания заказа
        \Log::channel('api_orders')->info('Order creation attempt', [
            'type_id' => $type_id,
            'ip' => $this->clientIp
        ]);

        return $this->makeRequest('add', $params);
    }


//    const HOST = 'https://partner.soc-proof.su/api/';
//
//    private $apiKey;
//
//    public function __construct($apiKey)
//    {
//        $this->apiKey = $apiKey;
//    }
//
//    /**
//     * Массив товаров (типов), содержит всю информацию
//     *
//     * @return mixed
//     * @throws GuzzleException
//     */
//    public function services()
//    {
//        try {
//            $requestParam = [
//                'key' => $this->apiKey,
//                'action' => 'services',
//            ];
//
//            $client = new Client(['base_uri' => self::HOST]);
//            $response = $client->post('v2', [
//                'form_params' => $requestParam, // Параметры передаются в теле POST-запроса
//            ]);
//
////            $client = new Client(['base_uri' => self::HOST]);
////            $response = $client->post('v2' . '?' . http_build_query($requestParam));
//
//            $result = $response->getBody()->getContents();
//            if (preg_match('/\berror\b/', $result)) {
//                BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . substr($this->apiKey, -10));
//                throw new RuntimeException('Ошибка API: ' . substr($this->apiKey, -10));
//            }
//
//            return json_decode($result, true);
//        } catch (RuntimeException $r) {
//            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
//            throw new RuntimeException('Ошибка в получении данных провайдера');
//        }
//    }
//
//    /**
//     * Статус заказа
//     *
//     * @param $order
//     * @return mixed
//     * @throws GuzzleException
//     */
//    public function status($order)
//    {
//        try {
//            $requestParam = [
//                'key' => $this->apiKey,
//                'action' => 'status',
//                'order' => $order
//            ];
//
//            $client = new Client(['base_uri' => self::HOST]);
//            $response = $client->post('v2', [
//                'form_params' => $requestParam, // Параметры передаются в теле POST-запроса
//            ]);
//
////            $client = new Client(['base_uri' => self::HOST]);
////            $response = $client->post('v2' . '?' . http_build_query($requestParam));
//
//            $result = $response->getBody()->getContents();
//            if (preg_match('/\berror\b/', $result)) {
//                BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . substr($this->apiKey, -10));
//                throw new RuntimeException('Ошибка API');
//            }
//
//            return json_decode($result, true);
//        } catch (RuntimeException $r) {
//            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
//            throw new RuntimeException('Ошибка в получении данных провайдера');
//        }
//    }
//
//    /**
//     * Создание заказа (пока работает Default, Poll)
//     *
//     * @param $type_id
//     * @param $link
//     * @param $quantity
//     * @param $comments
//     * @param $username
//     * @param $answer_number
//     * @param $min
//     * @param $max
//     * @param $posts
//     * @param $old_posts
//     * @param $delay
//     * @param $expiry
//     * @param $runs
//     * @param $interval
//     * @return mixed
//     * @throws GuzzleException
//     */
//    public function add(
//        $type_id,
//        $link = null, // Ссылка на страницу
//        $quantity = null, // Необходимое количество
//        $comments = null, // Список комментариев, разделенных символами \r\n или \n
//        $username = null, // URL-адрес для удаления подписчиков с
//        $answer_number = null, // Номер ответа в опросе
//        $min = null, // Минимальное количество
//        $max = null, // Максимальное количество
//        $posts = null, // Ограничить количество новых (будущих) записей, которые будут проанализированы и для которых будут создаваться заказы
//        $old_posts = null, // Количество существующих записей, которые будут проанализированы и для которых будут созданы заказы
//        $delay = null, // Задержка в минутах. Возможные значения: 0, 5, 10, 15, 30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 360, 420, 480, 540, 600
//        $expiry = null, // Дата истечения срока действия. Формат d/m/Y
//        $runs = null,
//        $interval = null
//    )
//    {
//        try {
//            $requestParam = [
//                'key' => $this->apiKey,
//                'action' => 'add',
//
//                'service' => $type_id,
//                'link' => $link,
//                'quantity' => $quantity,
//                'comments' => $comments,
//                'username' => $username,
//                'answer_number' => $answer_number,
//                'min' => $min,
//                'max' => $max,
//                'posts' => $posts,
//                'old_posts' => $old_posts,
//                'delay' => $delay,
//                'expiry' => $expiry,
//                'runs' => $runs,
//                'interval' => $interval,
//            ];
//
////            $client = new Client(['base_uri' => self::HOST]);
////            $response = $client->post('v2' . '?' . http_build_query($requestParam));
//
//            $client = new Client(['base_uri' => self::HOST]);
//            $response = $client->post('v2', [
//                'form_params' => $requestParam, // Параметры передаются в теле POST-запроса
//            ]);
//
//            $result = $response->getBody()->getContents();
//            if (preg_match('/\berror\b/', $result)) {
//                BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . substr($this->apiKey, -10));
//                throw new RuntimeException('Ошибка API');
//            }
//
//            return json_decode($result, true);
//        } catch (RuntimeException $r) {
//            BotLogHelpers::notifyBotLog('(🟣R ' . __FUNCTION__ . ' Smm): ' . $r->getMessage());
//            throw new RuntimeException('Ошибка в получении данных провайдера');
//        }
//    }
}
