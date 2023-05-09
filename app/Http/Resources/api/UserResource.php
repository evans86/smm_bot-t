<?php

namespace App\Http\Resources\api;

use App\Models\User\User;

class UserResource
{
    /**
     * @param User $user
     * @return array
     */
    public static function generateUserArray(User $user): array
    {
        return [
            'id' => (integer)$user->telegram_id,
            'language' => $user->language,
        ];
    }
}
