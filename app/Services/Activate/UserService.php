<?php

namespace App\Services\Activate;

use App\Models\User\SmsUser;
use App\Services\MainService;
use App\Services\External\ActivApi;
use RuntimeException;

class UserService extends MainService
{
    /**
     * Баланс с сервиса
     *
     * @return mixed
     */
    public function balance($bot)
    {
        try {
            $smsActivate = new ActivApi($bot->api_key, $bot->resource_link);
            $balance = $smsActivate->getBalance();
        } catch (\Exception $e) {
            $balance = '';
        }

        return $balance;
    }

    /**
     * Добавление пользователя
     *
     * @param int $telegram_id
     * @return SmsUser
     */
    public function getOrCreate(int $telegram_id): SmsUser
    {
        $user = SmsUser::query()->where(['telegram_id' => $telegram_id])->first();
        if (is_null($user)) {
            $user = new SmsUser();
            $user->telegram_id = $telegram_id;
            $user->language = SmsUser::LANGUAGE_RU;
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
     * @return SmsUser
     */
    public function updateLanguage(int $telegram_id, string $language): SmsUser
    {
        $user = SmsUser::query()->where(['telegram_id' => $telegram_id])->first();
        if (is_null($user)) {
            throw new RuntimeException('user not found');
        }

        if ($language != SmsUser::LANGUAGE_RU && $language != SmsUser::LANGUAGE_ENG)
            throw new RuntimeException('language not valid');
        $user->language = $language;

        if (!$user->save())
            throw new RuntimeException('user not save language');
        return $user;
    }

    /**
     * @param int $telegram_id
     * @param string $service
     * @return SmsUser
     */
    public function updateService(int $telegram_id, string $service): SmsUser
    {
        $user = SmsUser::query()->where(['telegram_id' => $telegram_id])->first();
        if (is_null($user)) {
            throw new RuntimeException('user not found');
        }
        $user->service = $service;

        if (!$user->save())
            throw new RuntimeException('user not save service');
        return $user;
    }

}
