@extends('layouts.main')
@section('content')
    <div class="container mt-2">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Сервис ID</th>
                <th scope="col">Пользователь</th>
                <th scope="col">Страна</th>
                <th scope="col">Заказ</th>
                <th scope="col">Статус</th>
                <th scope="col">Бот</th>
                <th scope="col">Создан в сервисе </th>
                <th scope="col">Окончание </th>
            </tr>
            </thead>
            <tbody>
            <tr>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->prolong_org_id }}</td>
                    <td>{{ $order->user_id }}</td>
                    <td>{{ $order->country->name_en }}<img src={{ $order->country->image }} width="24"></td>
                    <td>
                        Прокси: {{ $order->host }}:{{ $order->port }}
                        <br>Логин: {{ $order->user }}
                        <br>Пароль: {{ $order->pass }}
                        <br>Версия: {{ $order->proxy->title }}
                    </td>
                    <td>{!!\App\Helpers\OrdersHelper::statusLabel($order->status_org)!!}</td>
                    <td>{{ $order->bot_id }}</td>
                    <td>{{\Carbon\Carbon::createFromTimestamp($order->start_time)->toDateTimeString()}}</td>
                    <td>{{\Carbon\Carbon::createFromTimestamp($order->end_time)->toDateTimeString()}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex">
            {!! $orders->links() !!}
        </div>
    </div>
@endsection
