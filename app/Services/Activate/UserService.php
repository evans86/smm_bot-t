<?php

namespace App\Services\Activate;

use App\Models\User\User;
use App\Services\MainService;
use RuntimeException;

class UserService extends MainService
{
    /**
     * Добавление пользователя
     *
     * @param int $telegram_id
     * @return User
     */
    public function getOrCreate(int $telegram_id): User
    {
        $user = User::query()->where(['telegram_id' => $telegram_id])->first();
        if (is_null($user)) {
            $user = new User();
            $user->telegram_id = $telegram_id;
            $user->language = User::LANGUAGE_RU;
            if(!$user->save())
                throw new RuntimeException('user not created');
        }
        return $user;
    }

    /**
     * Обновление языка у пользователя
     *
     * @param int $telegram_id
     * @param string $language
     * @return User
     */
    public function updateLanguage(int $telegram_id, string $language): User
    {
        $user = User::query()->where(['telegram_id' => $telegram_id])->first();
        if (is_null($user)) {
            throw new RuntimeException('user not found');
        }

        if ($language != User::LANGUAGE_RU && $language != User::LANGUAGE_ENG)
            throw new RuntimeException('language not valid');
        $user->language = $language;

        if (!$user->save())
            throw new RuntimeException('user not save language');
        return $user;
    }
}
