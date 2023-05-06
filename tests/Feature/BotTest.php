<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BotTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_ping()
    {
        $response = $this->get('/ping');


        $response->assertStatus(200);
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":"OK"}');
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreate()
    {
        $response = $this->get('/create');

        $response->assertStatus(422);
        $this->assertEquals($response->baseResponse->content(), '{"result":false,"message":"The bot id field is required."}');

        $params = [
            'bot_id' => 1234,
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->get('/create?' . http_build_query($params), );
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"","category_id":0,"percent":5,"version":1,"resource_link":"https:\/\/api.sms-activate.org\/stubs\/handler_api.php"}}');
        $response->assertStatus(200);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGet()
    {
        $params = [
            'bot_id' => 1234,
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->get('/create?' . http_build_query($params), );
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"","category_id":0,"percent":5,"version":1,"resource_link":"https:\/\/api.sms-activate.org\/stubs\/handler_api.php"}}');
        $response->assertStatus(200);

        $params = [
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->get('/get?' . http_build_query($params));
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"","category_id":0,"percent":5,"version":1,"resource_link":"https:\/\/api.sms-activate.org\/stubs\/handler_api.php"}}');
        $response->assertStatus(200);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdate()
    {
        $params = [
            'bot_id' => 1234,
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->get('/create?' . http_build_query($params), );
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"","category_id":0,"percent":5,"version":1,"resource_link":"https:\/\/api.sms-activate.org\/stubs\/handler_api.php"}}');
        $response->assertStatus(200);

        $params = [
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->post('/update?' . http_build_query($params), [
            'bot_id' => 123,
            'api_key' => 'asdasd',
            'category_id' => 1234,
            'percent' => 5,
            'version' => '1',
            'resource_link' => '',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"asdasd","category_id":1234,"percent":5,"version":1,"resource_link":"https:\/\/api.sms-activate.org\/stubs\/handler_api.php"}}');

        $response = $this->post('/update?' . http_build_query($params), [
            'bot_id' => 123,
            'api_key' => 'asdasd',
            'category_id' => 1234,
            'percent' => 5,
            'version' => '1',
            'resource_link' => 'asdasdsadasd',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"asdasd","category_id":1234,"percent":5,"version":1,"resource_link":"https:\/\/api.sms-activate.org\/stubs\/handler_api.php"}}');

        $response = $this->post('/update?' . http_build_query($params), [
            'bot_id' => 123,
            'api_key' => 'asdasd',
            'category_id' => 1234,
            'percent' => 5,
            'version' => '1',
            'resource_link' => 'http://cheapsms.pro/stubs/handler_api.php',
        ]);
        $response->assertStatus(200);
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"asdasd","category_id":1234,"percent":5,"version":1,"resource_link":"http:\/\/cheapsms.pro\/stubs\/handler_api.php"}}');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDelete()
    {
        $params = [
            'bot_id' => 1234,
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->get('/create?' . http_build_query($params) );
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"","category_id":0,"percent":5,"version":1,"resource_link":"https:\/\/api.sms-activate.org\/stubs\/handler_api.php"}}');

        $response->assertStatus(200);


        $params = [
            'bot_id' => 1234,
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->get('/create?' . http_build_query($params));
        $this->assertEquals($response->baseResponse->content(), '{"result":false,"message":"The bot id has already been taken."}');
        $response->assertStatus(422);

        $params = [
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->get('/delete?' . http_build_query($params), );

        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":"OK"}');
        $response->assertStatus(200);

        $params = [
            'bot_id' => 1234,
            'public_key' => 'Public1',
            'private_key' => 'Private1',
        ];
        $response = $this->get('/create?' . http_build_query($params) );
        $this->assertEquals($response->baseResponse->content(), '{"result":true,"data":{"public_key":"Public1","private_key":"Private1","bot_id":1234,"api_key":"","category_id":0,"percent":5,"version":1,"resource_link":"https:\/\/api.sms-activate.org\/stubs\/handler_api.php"}}');

        $response->assertStatus(200);
    }
}
