{{--@extends('layouts.main')--}}
@extends('layouts.app', ['page' => __('Пользователи'), 'pageSlug' => 'users'])

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header">
                    <h4 class="card-title"> Пользователи</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table tablesorter " id="">
                            <thead class=" text-primary">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Telegram ID</th>
                                <th class="text-center">Язык</th>
                                <th class="text-center">Добален</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="text-center">{{ $user->id }}</td>
                                    <td class="text-center">{{ $user->telegram_id }}</td>
                                    <td class="text-center">{{ $user->language }}</td>
                                    <td class="text-center">{{ $user->created_at }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex">
                        {!! $users->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
