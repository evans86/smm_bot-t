<?php

namespace App\Helpers;

class ApiHelpers
{
    /**
     * Маскирует секреты, если в ответ случайно попал URL или текст исключения с query/form параметрами.
     */
    private static function sanitizeErrorMessage(string $message): string
    {
        $secretKeys = 'private_key|secret_key|user_secret_key|api_key|token';

        $message = preg_replace([
            '/((' . $secretKeys . ')=)[^&\s"\']+/i',
            '/((' . $secretKeys . ')%3D)[^&\s"\']+/i',
            '/(["\'](' . $secretKeys . ')["\']\s*:\s*["\'])[^"\']*(["\'])/i',
            '/((' . $secretKeys . ')\s*:\s*)[^\s,;&"\']+/i',
        ], [
            '$1[redacted]',
            '$1[redacted]',
            '$1[redacted]$3',
            '$1[redacted]',
        ], $message);

        return $message ?? 'Error';
    }

    /**
     * @param $result
     * @return array
     */
    public static function success($result): array
    {
        return [
            'result' => true,
            'data' => $result
        ];
    }

    /**
     * Ответ об ошибке в том же контракте, что и успех: всегда есть ключ {@see $}data (массив),
     * чтобы клиенты (например Bot-t) не падали на «в ответе не найден массив».
     *
     * @return array{result: bool, message: string, data: array<int, mixed>}
     */
    public static function error(string $message): array
    {
        return [
            'result' => false,
            'message' => self::sanitizeErrorMessage($message),
            'data' => [],
        ];
    }

    /**
     * @param string $result
     * @return array
     */
    public static function successStr(string $result): array
    {
        return [
            'result' => true,
            'data' => $result
        ];
    }

    /**
     * @param string $message
     * @return array
     */
    public static function errorNew(string $message): array
    {
        return [
            'result' => false,
            'message' => self::sanitizeErrorMessage($message)
        ];
    }

    /**
     * @param array $params
     * @param string $token
     * @return string
     */
    public static function generateSignature(array $params, string $token): string
    {
        $str = '';
        ksort($params);
        foreach ($params as $key => $param) {
            if (is_array($param))
                continue;
            $str .= $param . ':';
        }
        $str .= $token;
        return md5($str);
    }

    /**
     * @param array $gets
     * @param string $token
     * @return bool
     */
    public static function checkSignature(array $gets, string $token): bool
    {
        $signature = $gets['signature'];
        unset($gets['signature']);
        unset($gets['notification_id']);
        return self::generateSignature($gets, $token) === $signature;
    }
}
