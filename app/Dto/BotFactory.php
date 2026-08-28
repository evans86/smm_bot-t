<?php

namespace App\Dto;

use App\Models\Bot\Bot;

class BotFactory
{
    /**
     * @param Bot|\Illuminate\Database\Eloquent\Model|object $bot
     * @return BotDto
     */
    public static function fromEntity(Bot $bot): BotDto
    {
        try {
            return self::build($bot);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Bot DTO build failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private static function build(Bot $bot): BotDto
    {
        $dto = new BotDto();
        $dto->id = (int) $bot->id;
        $dto->public_key = (string) $bot->public_key;
        $dto->private_key = (string) ($bot->private_key ?? '');
        $dto->bot_id = (int) $bot->bot_id;
        $dto->api_key = $bot->api_key;
        $rawKey = $bot->getRawOriginal('api_key');
        $dto->setEncryptedApiKey($rawKey === null || $rawKey === '' ? null : (string) $rawKey);
        $dto->category_id = (int) $bot->category_id;
        $dto->percent = (int) $bot->percent;
        $dto->version = (int) $bot->version;
        $dto->color = (int) $bot->color;
        $dto->is_saved = $bot->is_saved === null ? null : (bool) $bot->is_saved;
        $dto->black = $bot->black === null ? null : (string) $bot->black;
        $dto->white = $bot->white === null ? null : (string) $bot->white;
        $dto->resource_link = $bot->resource_link === null ? null : (string) $bot->resource_link;
        return $dto;
    }
}
