<?php

namespace App\Services\Activate\Order\Strategy;

use App\Dto\BotDto;
use Illuminate\Http\Request;

class MainConcreteStrategy
{
    /**
     * @var BotDto
     */
    protected BotDto $botDto;

    public function __construct($botDto)
    {
        $this->botDto = $botDto;
    }

}
