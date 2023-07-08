{{--@extends('layouts.main')--}}
@extends('layouts.app', ['page' => __('Заказы'), 'pageSlug' => 'orders'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <h4 class="card-title"> Заказы</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                            <tr>
                                <th class="text-center">Общее число заказов</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th class="text-center">{{ $allCount }}</th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Order ID</th>
                                <th class="text-center">Пользователь</th>
                                <th class="text-center">Тип заказа</th>
                                <th class="text-center">Заказ</th>
                                <th class="text-center">Статус</th>
                                <th class="text-center">Бот</th>
                                <th class="text-center">Создан в сервисе </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td class="text-center">{{ $order->id }}</td>
                                    <td class="text-center">{{ $order->order_id }}</td>
                                    <td class="text-center">{{ $order->user_id }}</td>
                                    <td class="text-center">{{ $order->type }}</td>
                                    <td class="text-center">
                                        {{ $order->type_name }} (#{{ $order->type_id }})
                                        <br>Link: <code>{{ $order->link }}</code>
                                        <br>Цена: {{ ($order->price) / 100 }} р.
                                        <br>Количество: {{ $order->start_count }}
                                    </td>
                                    <td class="text-center">{!!\App\Helpers\OrdersHelper::statusLabel($order->status)!!}</td>
                                    <td class="text-center">{{ $order->bot_id }}</td>
                                    <td class="text-center">{{$order->created_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex">
                        {!! $orders->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
