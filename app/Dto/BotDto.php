<?php

namespace App\Dto;

class BotDto
{
    public $id;
    public $public_key;
    public $private_key;
    public $bot_id;
    public $api_key;
    private $encrypted_api_key = null;
    public $category_id;
    public $percent;
    public $version;
    public $color;
    public $is_saved;
    public $black;
    public $white;
    public $resource_link;

    public function getEncryptedApiKey(): string
    {
        if ($this->encrypted_api_key === null) {
            throw new \RuntimeException('Encrypted API key not available');
        }
        return $this->encrypted_api_key;
    }

    public function setEncryptedApiKey(?string $encryptedKey): void
    {
        $this->encrypted_api_key = $encryptedKey;
    }

    public function getArray(): array
    {
        return [
            'id' => $this->id,
            'public_key' => $this->public_key,
            'private_key' => $this->private_key,
            'bot_id' => $this->bot_id,
            'api_key' => $this->api_key,
            'category_id' => $this->category_id,
            'percent' => $this->percent,
            'version' => $this->version,
            'color' => $this->color,
            'black' => $this->black,
            'white' => $this->white,
        ];
    }

    public function getSettings(): array
    {
        return [
            'color' => $this->color,
            'black' => $this->black,
            'white' => $this->white,
            'is_saved' => $this->is_saved,
        ];
    }
}
