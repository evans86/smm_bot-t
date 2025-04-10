<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Helpers\ApiHelpers;
use App\Models\Bot\Bot;
use App\Services\MainService;

class BotService extends MainService
{
    const DEFAULT_HOST = 'https://partner.soc-proof.su/api/v2';

    /**
     * Создание модуля
     *
     * @param string $public_key
     * @param string $private_key
     * @param int $bot_id
     * @return Bot
     */
    public function create(string $public_key, string $private_key, int $bot_id): Bot
    {
        $bot = new Bot();
        $bot->public_key = $public_key;
        $bot->private_key = $private_key;
        $bot->bot_id = $bot_id;
        $bot->api_key = '';
        $bot->category_id = 0;
        $bot->percent = 5;
        $bot->version = 3;
        $bot->color = 1;
        $bot->black = null;
        $bot->white = null;
        $bot->resource_link = self::DEFAULT_HOST;
        if (!$bot->save())
            throw new \RuntimeException('bot dont save');
        return $bot;
    }

    /**
     * Обновление настроек модуля
     *
     * @param BotDto $dto
     * @return Bot
     */
    public function update(BotDto $dto): Bot
    {
        $bot = Bot::query()
            ->where('public_key', $dto->public_key)
            ->where('private_key', $dto->private_key)
            ->first();

        if (empty($bot))
            return ApiHelpers::error('Not found module.');

        // Обновляем только если ключ не замаскирован
        if (strpos($dto->api_key, '****') === false) {
            $bot->api_key = $dto->api_key; // Автоматически зашифруется через мутатор
        }
        $bot->version = $dto->version;
        $bot->percent = $dto->percent;
        $bot->color = $dto->color;
        $bot->black = $dto->black;
        $bot->white = $dto->white;
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
        $bot = Bot::query()->where('public_key', $public_key)->where('private_key', $private_key)->first();
        if (empty($bot))
            throw new \RuntimeException('Not found module.');
        if (!$bot->delete())
            throw new \RuntimeException('bot dont delete');
    }
}
