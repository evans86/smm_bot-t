<?php

namespace App\Helpers;

use App\Services\Activate\UserService;

class BotHelpers
{
    /**
     * @param $bot
     * @return mixed
     */
    public static function balance($bot)
    {
        $userService = new UserService();
        return $userService->balance($bot);
    }
}
