
<?php

use App\Models\User\SmsUser;
use App\Services\Activate\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;
    private UserService $userService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->userService = new UserService();
        parent::__construct($name, $data, $dataName);
    }

    public function testCreate()
    {
        $user = $this->userService->getOrCreate(
            $telegram_id = 123
        );
        self::assertEquals($user->telegram_id, $telegram_id);

        $user_2 = $this->userService->getOrCreate(
            $telegram_id = 123
        );
        self::assertEquals($user->id, $user_2->id);
        self::assertEquals($user_2->telegram_id, $telegram_id);
    }

    public function testUpdateLanguage()
    {
        $user = $this->userService->getOrCreate(
            $telegram_id = 123
        );
        self::assertEquals($user->telegram_id, $telegram_id);
        self::assertEquals($user->language, SmsUser::LANGUAGE_RU);

        $user = $this->userService->updateLanguage(
            $telegram_id = 123, SmsUser::LANGUAGE_ENG
        );
        self::assertEquals($user->language, SmsUser::LANGUAGE_ENG);

        try {
            $user_2 = $this->userService->updateLanguage(
                $telegram_id = 123, 'asdasd'
            );
            self::assertEquals(1,2);
        } catch (RuntimeException $e) {
            self::assertEquals($e->getMessage(),'language not valid');
        }
    }

    public function testUpdateService()
    {
        $user = $this->userService->getOrCreate(
            $telegram_id = 123
        );
        self::assertEquals($user->telegram_id, $telegram_id);

        $user = $this->userService->updateService(
            $telegram_id = 123, $service = 'asd'
        );
        self::assertEquals($user->service, $service);

    }
}
