<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class BotLogHelpers
{
    public static function notifyBotLog($message)
    {
        $client = new Client();

        $client->post('https://api.telegram.org/bot6355061130:AAEKDaNvf22iRtd7-E5o5hUxNyMgn0ZIcMM/sendMessage', [

            RequestOptions::JSON => [
                'chat_id' => 398981226,
                'text' => $message,
            ]
        ]);
    }
}
