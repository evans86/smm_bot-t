<?php

namespace App\Helpers;

use App\Models\Order\Order;
use App\Models\Proxy\Proxy;

class OrdersHelper
{
    public static function statusList(): array
    {
        return [
            Order::ORDER_DELETE => 'Удалено',
            Order::ORDER_ACTIVE => 'Активен',
            Order::ORDER_FINISH => 'Окончен',
        ];
    }

    public static function statusLabel($status): string
    {
        switch ($status) {
            case Order::ORDER_DELETE:
                $class = 'badge bg-danger';
                break;
            case Order::ORDER_ACTIVE:
                $class = 'badge bg-success';
                break;
            case Order::ORDER_FINISH:
                $class = 'badge bg-warning';
                break;
        }


        return '<span class="' . $class . '">' . \Arr::get(self::statusList(), $status) . '</span>';
    }
}
