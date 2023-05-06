
<?php

use App\Services\Activate\BotService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BotServiceTest extends \Tests\TestCase
{
    use RefreshDatabase;
    private BotService $botService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->botService = new BotService();
        parent::__construct($name, $data, $dataName);
    }

    public function testCreate()
    {
        $bot = $this->botService->create(
            $public_key = 'public',
            $private_key = 'private',
            $bot_id = 123,
        );
        self::assertEquals($bot->public_key, $public_key);
        self::assertEquals($bot->private_key, $private_key);
        self::assertEquals($bot->bot_id, $bot_id);
        self::assertEquals($bot->resource_link, BotService::DEFAULT_HOST);
    }
}
