<?php

namespace App\Http\Controllers\Activate;

use App\Models\Order\Order;

class OrderController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $orders = Order::orderBy('id', 'DESC')->Paginate(15);

        return view('activate.order.index', compact(
            'orders',
        ));
    }
}
