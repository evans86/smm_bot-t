<?php

namespace App\Helpers;

use App\Services\Activate\UserService;

class BotHelpers
{
    public static function balance($bot)
    {
        $userService = new UserService();
        return $userService->balance($bot);
    }
}
