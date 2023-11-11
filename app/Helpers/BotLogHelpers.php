<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class BotLogHelpers
{
    public static function notifyBotLog($message)
    {
        $client = new Client();

        $client->post('https://api.telegram.org/bot6967494667:AAHx-f9rORNBcHM7DqTUx2EBhGTxVVUvesA/sendMessage', [

            RequestOptions::JSON => [
                'chat_id' => 6715142449,
                'text' => $message,
            ]
        ]);
    }
}
