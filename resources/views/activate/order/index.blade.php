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
                                <th class="text-center">Общее число прокси</th>
                                <th class="text-center">Активных прокси</th>
                                <th class="text-center">Удаленных прокси</th>
                                <th class="text-center">Оконченных прокси</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th class="text-center">{{ $allCount }}</th>
                                <th class="text-center">{{ $activeCount }}</th>
                                <th class="text-center">{{ $deleteCount }}</th>
                                <th class="text-center">{{ $finishCount }}</th>
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
                                <th class="text-center">Сервис ID</th>
                                <th class="text-center">Пользователь</th>
                                <th class="text-center">Страна</th>
                                <th class="text-center">Заказ</th>
                                <th class="text-center">Статус</th>
                                <th class="text-center">Бот</th>
                                <th class="text-center">Создан в сервисе </th>
                                <th class="text-center">Окончание </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td class="text-center">{{ $order->id }}</td>
                                    <td class="text-center">{{ $order->prolong_org_id }}</td>
                                    <td class="text-center">{{ $order->user_id }}</td>
                                    <td class="text-center">{{ $order->country->name_en }}<img src={{ $order->country->image }} width="24"></td>
                                    <td class="text-center">
                                        Прокси: <code>{{ $order->host }}:{{ $order->port }}</code>
                                        <br>Логин: <code>{{ $order->user }}</code>
                                        <br>Пароль: <code>{{ $order->pass }}</code>
                                        <br>Версия: <code>{{ $order->proxy->title }}</code>
                                    </td>
                                    <td class="text-center">{!!\App\Helpers\OrdersHelper::statusLabel($order->status_org)!!}</td>
                                    <td class="text-center">{{ $order->bot_id }}</td>
                                    <td class="text-center">{{\Carbon\Carbon::createFromTimestamp($order->start_time)->toDateTimeString()}}</td>
                                    <td class="text-center">{{\Carbon\Carbon::createFromTimestamp($order->end_time)->toDateTimeString()}}</td>
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
