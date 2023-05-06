<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Helpers\ApiHelpers;
use App\Models\Bot\SmsBot;
use App\Services\MainService;

class BotService extends MainService
{
    const DEFAULT_HOST = 'https://api.sms-activate.org/stubs/handler_api.php';

    /**
     * Создание модуля
     *
     * @param string $public_key
     * @param string $private_key
     * @param int $bot_id
     * @return SmsBot
     */
    public function create(string $public_key, string $private_key, int $bot_id): SmsBot
    {
        $bot = new SmsBot();
        $bot->public_key = $public_key;
        $bot->private_key = $private_key;
        $bot->bot_id = $bot_id;
        $bot->api_key = '';
        $bot->category_id = 0;
        $bot->percent = 5;
        $bot->version = 1;
        $bot->resource_link = self::DEFAULT_HOST;
        if(!$bot->save())
            throw new \RuntimeException('bot dont save');
        return $bot;
    }

    /**
     * Обновление настроек модуля
     *
     * @param BotDto $dto
     * @return SmsBot
     */
    public function update(BotDto $dto): SmsBot
    {
        $bot = SmsBot::query()->where('public_key', $dto->public_key)->where('private_key', $dto->private_key)->first();
        if (empty($bot))
            return ApiHelpers::error('Not found module.');
        $bot->version = $dto->version;
        $bot->percent = $dto->percent;
        $bot->api_key = $dto->api_key;
        $bot->category_id = $dto->category_id;
        $bot->resource_link = $dto->resource_link;
        if (!$bot->save())
            throw new \RuntimeException('bot dont save');
        return $bot;
    }

    /**
     * Удаление модуля
     *
     * @param string $public_key
     * @param string $private_key
     * @return void
     */
    public function delete(string $public_key, string $private_key): void
    {
        $bot = SmsBot::query()->where('public_key', $public_key)->where('private_key', $private_key)->first();
        if (empty($bot))
            throw new \RuntimeException('Not found module.');
        if (!$bot->delete())
            throw new \RuntimeException('bot dont delete');
    }
}
