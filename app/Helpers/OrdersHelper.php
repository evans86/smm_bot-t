<?php

namespace App\Helpers;

class OrdersHelper
{
    public static function statusList(): array
    {
        return [
            0 => 'Удалено',
            1 => 'Активен',
        ];
    }

    public static function statusLabel($status): string
    {
        switch ($status) {
            case 0:
                $class = 'badge bg-danger';
                break;
            case 1:
                $class = 'badge bg-success';
                break;
        }


        return '<span class="' . $class . '">' . \Arr::get(self::statusList(), $status) . '</span>';
    }
}
