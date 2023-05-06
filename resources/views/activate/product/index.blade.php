@extends('layouts.main')
@section('content')
    <div class="container mt-5">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">Сервис</th>
                <th scope="col">Количество</th>
                <th scope="col">Картинка</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            @foreach($products as $key => $product)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ $product }}</td>
                    <td><img class="service_img"
                             src="https://smsactivate.s3.eu-central-1.amazonaws.com/assets/ico/{{ $key }}0.webp"
                             width="24"></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
