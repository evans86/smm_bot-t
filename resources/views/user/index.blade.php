@extends('layouts.main')
@section('content')
    <div class="container mt-2">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Telegram ID</th>
                <th scope="col">Сервис</th>
                <th scope="col">Язык</th>
                <th scope="col">Добален</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->telegram_id }}</td>
                    <td><img class="service_img"
                             src="https://smsactivate.s3.eu-central-1.amazonaws.com/assets/ico/{{ $user->service }}0.webp"
                             width="24"></td>
                    <td>{{ $user->language }}</td>
                    <td>{{ $user->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex">
            {!! $users->links() !!}
        </div>
    </div>
@endsection
