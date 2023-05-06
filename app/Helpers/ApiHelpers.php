<?php

namespace App\Helpers;

class ApiHelpers
{
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
     * @param string $message
     * @return string
     */
    public static function error(string $message): string
    {
        $result = [
            'result' => false,
            'message' => $message
        ];
        return json_encode($result);
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
            'message' => $message
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
