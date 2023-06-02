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

        $allCount = count(Order::get());
        $activeCount = count(Order::query()->where('status_org', Order::ORDER_ACTIVE)->get());
        $finishCount = count(Order::query()->where('status_org', Order::ORDER_FINISH)->get());
        $deleteCount = count(Order::query()->where('status_org', Order::ORDER_DELETE)->get());

        return view('activate.order.index', compact(
            'orders',
            'allCount',
            'activeCount',
            'finishCount',
            'deleteCount',
        ));
    }
}
