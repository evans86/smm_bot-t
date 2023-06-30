<?php

namespace App\Services\External;

use GuzzleHttp\Client;

class PartnerApi
{
    const HOST = 'https://partner.soc-proof.su/api/';

    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function services()
    {
        $requestParam = [
            'key' => $this->apiKey,
            'action' => __FUNCTION__,
        ];

        $client = new Client(['base_uri' => self::HOST]);
        $response = $client->post('v2' . '?' . http_build_query($requestParam));

        $result = $response->getBody()->getContents();
        return json_decode($result, true);
    }
}
