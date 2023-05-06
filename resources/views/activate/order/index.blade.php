@extends('layouts.main')
@section('content')
    <div class="container mt-2">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Сервис ID</th>
                <th scope="col">Пользователь</th>
                <th scope="col">Номер телефона</th>
                <th scope="col">Страна</th>
                <th scope="col">Оператор</th>
                <th scope="col">Сервис</th>
                <th scope="col">Статус</th>
                <th scope="col">Коды</th>
                <th scope="col">Бот</th>
                <th scope="col">Создан в сервисе </th>
            </tr>
            </thead>
            <tbody>
            <tr>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->org_id }}</td>
                    <td>{{ $order->user->telegram_id }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>{{ $order->country->name_en }}<img src={{ $order->country->image }} width="24"></td>
                    <td>{{ $order->operator }}</td>
                    <td><img class="service_img"
                             src="https://smsactivate.s3.eu-central-1.amazonaws.com/assets/ico/{{ $order->service }}0.webp"
                             width="24"></td>
                    <td>{!!\App\Helpers\OrdersHelper::statusLabel($order->status)!!}</td>
                    <td>{{ $order->codes }}</td>
                    <td>{{ $order->bot_id }}</td>
                    <td>{{\Carbon\Carbon::createFromTimestamp($order->start_time)->toDateTimeString()}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex">
            {!! $orders->links() !!}
        </div>
    </div>
@endsection
