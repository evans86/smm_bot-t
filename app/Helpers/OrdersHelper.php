<?php

namespace App\Helpers;

use App\Models\Order\Order;
use App\Models\Proxy\Proxy;

class OrdersHelper
{
    public static function statusList(): array
    {
        return [
            Order::CREATE_STATUS => 'Ожидание',
            Order::TO_PROCESS_STATUS => 'Выполняется',
            Order::FINISH_STATUS => 'Завершен',
            Order::WORK_STATUS => 'Выполняется',
        ];
    }

    public static function statusLabel($status): string
    {
        switch ($status) {
            case Order::CREATE_STATUS:
                $class = 'badge bg-info text-dark';
                break;
            case Order::FINISH_STATUS:
                $class = 'badge bg-success text-dark';
                break;
            case Order::TO_PROCESS_STATUS:
            case Order::WORK_STATUS:
                $class = 'badge bg-warning text-dark';
                break;
            default:
                $class = 'badge bg-secondary';
        }


        return '<span class="' . $class . '">' . \Arr::get(self::statusList(), $status) . '</span>';
    }

    /**
     * @param $result
     * @return false|int
     */
    public static function requestArray($result)
    {
        $errorCodes = [
            'neworder.error.link_duplicate' => 'Ошибка создания нового заказа: сслыка дублируется',
            'neworder.error.not_enough_funds' => 'Ошибка создания нового заказа: админимтратор должен пополнить баланс на сервисе'
        ];

        if (array_key_exists($result, $errorCodes)) {
            return $errorCodes[$result];
        } else {
            return $result;
        }
    }
}
