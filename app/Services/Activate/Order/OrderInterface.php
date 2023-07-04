<?php

namespace App\Services\Activate\Order;

use App\Models\Order\Order;
use Illuminate\Http\Request;

interface OrderInterface
{
    /**
     * создание заказа
     *
     * @param Request $request
     * @return array
     */
    public function create(Request $request): array;
}
