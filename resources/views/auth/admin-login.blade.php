@extends('layouts.app', ['class' => 'login-page', 'page' => __('Вход в панель'), 'contentClass' => 'login-page'])

@section('content')
    <div class="col-lg-4 col-md-6 ml-auto mr-auto">
        @if(session('warning'))
            <div class="alert alert-warning text-white mb-3" role="alert">{{ session('warning') }}</div>
        @endif
        @if(empty($adminConfigured))
            <div class="alert alert-warning text-white mb-3" role="alert">
                Задайте в .env переменные <code>ADMIN_USERNAME</code> и <code>ADMIN_PASSWORD_BCRYPT</code> (или <code>ADMIN_PASSWORD</code> только для локальной разработки).
            </div>
        @endif
        <form class="form" method="post" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="card card-login card-white">
                <div class="card-header">
                    <img src="{{ asset('black') }}/img/card-primary.png" alt="">
                    <h1 class="card-title">{{ __('SMM') }}</h1>
                </div>
                <div class="card-body">
                    <div class="input-group{{ $errors->has('username') ? ' has-danger' : '' }}">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="tim-icons icon-user-run"></i>
                            </div>
                        </div>
                        <input type="text" name="username" value="{{ old('username') }}" autocomplete="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="{{ __('Логин') }}" required autofocus>
                        @include('alerts.feedback', ['field' => 'username'])
                    </div>
                    <div class="input-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="tim-icons icon-lock-circle"></i>
                            </div>
                        </div>
                        <input type="password" placeholder="{{ __('Пароль') }}" name="password" autocomplete="current-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required>
                        @include('alerts.feedback', ['field' => 'password'])
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-lg btn-block mb-3">{{ __('Войти') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
