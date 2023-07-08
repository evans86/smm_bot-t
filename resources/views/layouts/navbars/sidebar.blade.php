<div class="sidebar">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="/" class="simple-text logo-normal">{{ __('SMM') }}</a>
        </div>
        <ul class="nav">
            <li @if ($pageSlug == 'countries') class="active " @endif>
                <a href="{{ route('activate.social.index') }}">
                    <i class="tim-icons icon-world"></i>
                    <p>{{ __('Список соц.сетей') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'users') class="active " @endif>
                <a href="{{ route('users.index') }}">
                    <i class="tim-icons icon-single-02"></i>
                    <p>{{ __('Пользователи') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'orders') class="active " @endif>
                <a href="{{ route('activate.order.index') }}">
                    <i class="tim-icons icon-send"></i>
                    <p>{{ __('Заказы') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'bots') class="active " @endif>
                <a href="{{ route('activate.bot.index') }}">
                    <i class="tim-icons icon-controller"></i>
                    <p>{{ __('Боты') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'icons') class="active " @endif>
                <a href="{{ route('pages.icons') }}">
                    <i class="tim-icons icon-atom"></i>
                    <p>{{ __('Icons') }}</p>
                </a>
            </li>
        </ul>
    </div>
</div>
