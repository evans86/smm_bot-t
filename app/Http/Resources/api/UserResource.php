<?php

namespace App\Http\Resources\api;

use App\Models\User\SmsUser;

class UserResource
{
    /**
     * @param SmsUser $user
     * @return array
     */
    public static function generateUserArray(SmsUser $user): array
    {
        return [
            'id' => (integer)$user->telegram_id,
            'language' => $user->language,
            'service' => $user->service
        ];
    }
}
