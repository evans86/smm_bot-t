@extends('layouts.main')
@section('content')
    <div class="container mt-2">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">ISO</th>
                <th scope="col">RU</th>
                <th scope="col">EN</th>
                <th scope="col">Icon</th>
                <th scope="col">Добалено</th>
            </tr>
            </thead>
            <tbody>
            <tr>
            @foreach($countries as $country)
                <tr>
                    <td>{{ $country->id }}</td>
                    <td>{{ $country->iso_two }}</td>
                    <td>{{ $country->name_ru }}</td>
                    <td>{{ $country->name_en }}</td>
                    <td><img src={{ $country->image }} width="24">
                    </td>
                    <td>{{ $country->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex">
            {!! $countries->links() !!}
        </div>
    </div>
@endsection

