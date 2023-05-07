<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ url('css/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css' )}}">
    <link rel="stylesheet" href="{{ asset('css/flag-icon.css' )}}"/>
    <title>Proxy</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100" style="background-image: url('{{ asset('img/bg.svg')}}');">
<div class="container">
    <div class="row">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">

                <a class="navbar-brand" href="{{ route('home') }}">Главная</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false"
                        aria-label="Переключатель навигации">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest

                        <a class="nav-link" href="{{ route('activate.countries.index') }}">Список стран</a>
                        <a class="nav-link" href="{{ route('users.index') }}">Пользователи</a>
                        <a class="nav-link" href="{{ route('activate.order.index') }}">Заказы</a>
                        <a class="nav-link" href="{{ route('activate.bot.index') }}">Боты</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    @yield('content')
</div>

<footer class="mt-auto">
    <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
        Copyright © 2023 | Сreated by:
        <a class="text-reset fw-bold" href="https://github.com/evans86">Rex</a>
        | Powered By:
        <a class="text-reset fw-bold" href="https://bot-t.com/">Bot-t.com</a>
    </div>
</footer>
</body>
</html>
