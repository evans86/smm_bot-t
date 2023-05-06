<?php

namespace App\Http\Controllers\Activate;

use App\Models\Order\SmsOrder;

class OrderController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $orders = SmsOrder::orderBy('id', 'DESC')->Paginate(15);

        return view('activate.order.index', compact(
            'orders',
        ));
    }
}
