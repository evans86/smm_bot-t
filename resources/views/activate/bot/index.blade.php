@extends('layouts.main')
@section('content')
    <div class="container mt-2">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Ключи</th>
                <th scope="col">Bot-t ID</th>
                <th scope="col">Версия</th>
                <th scope="col">Links</th>
                <th scope="col">ID категории</th>
                <th scope="col">Процент</th>
{{--                <th scope="col">Баланс</th>--}}
                <th scope="col">Создан</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            @foreach($bots as $bot)
                <tr>
                    <td>{{ $bot->id }}</td>
                    <td>Private: {{ $bot->public_key }}<br>Public: {{ $bot->private_key }}</td>
                    <td>{{ $bot->bot_id }}</td>
                    <td>{{ $bot->version }}</td>
                    <td>API key: {{ $bot->api_key }}<br>Link: {{ $bot->resource_link }}</td>
                    <td>{{ $bot->category_id }}</td>
                    <td>{{ $bot->percent }} %</td>
{{--                    <td>{{ \App\Helpers\BotHelpers::balance($bot) }} руб.</td>--}}
                    <td>{{ $bot->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex">
            {!! $bots->links() !!}
        </div>
    </div>
@endsection
