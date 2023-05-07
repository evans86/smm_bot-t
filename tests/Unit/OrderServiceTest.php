<?php

use App\Dto\BotFactory;
use App\Models\Order\Order;
use App\Services\Activate\BotService;
use App\Services\Activate\CountryService;
use App\Services\Activate\OrderService;
use App\Services\Activate\UserService;
use App\Services\External\BottApi;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Баланс в боте 15 рублей
 * Баланс на сайте примерно 55-65 рублей
 * Надо верно выбирать сервисы и страны для корректной проверки
 */
class OrderServiceTest extends \Tests\TestCase
{

    use RefreshDatabase;

    private BotService $botService;
    private OrderService $orderService;
    private CountryService $countryService;
    private UserService $userService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->botService = new BotService();
        $this->orderService = new OrderService();
        $this->countryService = new CountryService();
        $this->userService = new UserService();
        parent::__construct($name, $data, $dataName);
    }

    public function testFullPath()
    {
        $this->countryService->getApiCountries();
        $bot = $this->botService->create(
            $public_key = '062d7c679ca22cf88b01b13c0b24b057',
            $private_key = 'd75bee5e605d87bf6ebd432a2b25eb0e',
            $bot_id = 123,
        );
        $secret_key = '29978beb742581e93e31ec12ac518b76299755483b9614b8';
        self::assertEquals($bot->public_key, $public_key);
        self::assertEquals($bot->private_key, $private_key);
        self::assertEquals($bot->bot_id, $bot_id);
        self::assertEquals($bot->resource_link, BotService::DEFAULT_HOST);
        self::assertEquals($bot->api_key, '');

        $dto = BotFactory::fromEntity($bot);
        $dto->api_key = config('services.key_activate.key');
        $bot = $this->botService->update($dto);
        self::assertEquals($bot->api_key, $dto->api_key);


        $user = $this->userService->getOrCreate($telegram_id = 398981226);

        $botDto = BotFactory::fromEntity($bot);
        $result = BottApi::checkUser(
            $telegram_id,
            $secret_key,
            $botDto->public_key,
            $botDto->private_key
        );
        try {
            $this->orderService->create(
                $result['data'],
                BotFactory::fromEntity($bot),
                0,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('Choose service pls', $e->getMessage());
        }
        $user = $this->userService->updateService($telegram_id, 'tg');
        try {
            $this->orderService->create(
                $result['data'],
                BotFactory::fromEntity($bot),
                1,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('Создатель бота должен пополнить баланс в сервисе', $e->getMessage());
        }
        $user = $this->userService->updateService($telegram_id, 'fb');
        try {
            $this->orderService->create(
                $result['data'],
                BotFactory::fromEntity($bot),
                1,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('Пополните баланс в боте', $e->getMessage());
        }

        $user = $this->userService->updateService($telegram_id, 'ig');
        $orderData = $this->orderService->create(
            $result['data'],
            BotFactory::fromEntity($bot),
            0,
        );
        $result = BottApi::checkUser(
            $telegram_id,
            $secret_key,
            $botDto->public_key,
            $botDto->private_key
        );
        self::assertEquals($result['data']['money'], '450');
    }

    public function testCloseOrder()
    {
        $this->countryService->getApiCountries();
        $bot = $this->botService->create(
            $public_key = '062d7c679ca22cf88b01b13c0b24b057',
            $private_key = 'd75bee5e605d87bf6ebd432a2b25eb0e',
            $bot_id = 123,
        );
        $secret_key = '29978beb742581e93e31ec12ac518b76299755483b9614b8';
        self::assertEquals($bot->public_key, $public_key);
        self::assertEquals($bot->private_key, $private_key);
        self::assertEquals($bot->bot_id, $bot_id);
        self::assertEquals($bot->resource_link, BotService::DEFAULT_HOST);
        self::assertEquals($bot->api_key, '');

        $dto = BotFactory::fromEntity($bot);
        $dto->api_key = config('services.key_activate.key');
        $dto->category_id = 1194937;
        $bot = $this->botService->update($dto);
        self::assertEquals($bot->api_key, $dto->api_key);

        $user = $this->userService->getOrCreate($telegram_id = 398981226);

        $botDto = BotFactory::fromEntity($bot);
        $result = BottApi::checkUser(
            $telegram_id,
            $secret_key,
            $botDto->public_key,
            $botDto->private_key
        );

        $user = $this->userService->updateService($telegram_id, 'ig');

        $orderCreate = $this->orderService->create(
            $result['data'],
            BotFactory::fromEntity($bot),
            '0',
        );

        $order = Order::query()->where(['org_id' => $orderCreate['id']])->first();

        // Отмена заказа
        $orderData = $this->orderService->cancel(
            $result['data'],
            BotFactory::fromEntity($bot),
            $order,
        );

        self::assertEquals($order->status, Order::STATUS_CANCEL);

        //Отмена заказа со статусом 8 и пустыми кодами

        $order->status = Order::STATUS_CANCEL;
        try {
            $orderData = $this->orderService->cancel(
                $result['data'],
                BotFactory::fromEntity($bot),
                $order,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('The order has already been canceled', $e->getMessage());
        }

        $order->status = Order::ACCESS_ACTIVATION;
        try {
            $orderData = $this->orderService->cancel(
                $result['data'],
                BotFactory::fromEntity($bot),
                $order,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('The order has not been canceled, the number has been activated, Status 6', $e->getMessage());
        }

        $order->status = Order::STATUS_WAIT_CODE;
        $order->codes = '[123-132]';
        try {
            $orderData = $this->orderService->cancel(
                $result['data'],
                BotFactory::fromEntity($bot),
                $order,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('The order has not been canceled, the number has been activated', $e->getMessage());
        }
    }

    public function testConfirmOrder()
    {
        $this->countryService->getApiCountries();
        $bot = $this->botService->create(
            $public_key = '062d7c679ca22cf88b01b13c0b24b057',
            $private_key = 'd75bee5e605d87bf6ebd432a2b25eb0e',
            $bot_id = 123,
        );
        $secret_key = '29978beb742581e93e31ec12ac518b76299755483b9614b8';
        self::assertEquals($bot->public_key, $public_key);
        self::assertEquals($bot->private_key, $private_key);
        self::assertEquals($bot->bot_id, $bot_id);
        self::assertEquals($bot->resource_link, BotService::DEFAULT_HOST);
        self::assertEquals($bot->api_key, '');

        $dto = BotFactory::fromEntity($bot);
        $dto->api_key = config('services.key_activate.key');
        $bot = $this->botService->update($dto);
        self::assertEquals($bot->api_key, $dto->api_key);

        $user = $this->userService->getOrCreate($telegram_id = 398981226);

        $botDto = BotFactory::fromEntity($bot);
        $result = BottApi::checkUser(
            $telegram_id,
            $secret_key,
            $botDto->public_key,
            $botDto->private_key
        );

        $user = $this->userService->updateService($telegram_id, 'ig');

        $orderCreate = $this->orderService->create(
            $result['data'],
            BotFactory::fromEntity($bot),
            '0',
        );

        $order = Order::query()->where(['org_id' => $orderCreate['id']])->first();

        try {
            $orderData = $this->orderService->confirm(
                BotFactory::fromEntity($bot),
                $order,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('Попытка установить несуществующий статус', $e->getMessage());
        }

        $order->status = Order::STATUS_CANCEL;

        try {
            $orderData = $this->orderService->confirm(
                BotFactory::fromEntity($bot),
                $order,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('The order has already been canceled', $e->getMessage());
        }

        $order->end_time = time() - 86000;
        $order->codes = '[123-132]';

        try {
            $orderData = $this->orderService->confirm(
                BotFactory::fromEntity($bot),
                $order,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('Activation is suspended', $e->getMessage());
        }

        self::assertEquals($order->status, Order::ACCESS_ACTIVATION);
    }

    public function testSecondSms()
    {
        $this->countryService->getApiCountries();
        $bot = $this->botService->create(
            $public_key = '062d7c679ca22cf88b01b13c0b24b057',
            $private_key = 'd75bee5e605d87bf6ebd432a2b25eb0e',
            $bot_id = 123,
        );
        $secret_key = '29978beb742581e93e31ec12ac518b76299755483b9614b8';
        self::assertEquals($bot->public_key, $public_key);
        self::assertEquals($bot->private_key, $private_key);
        self::assertEquals($bot->bot_id, $bot_id);
        self::assertEquals($bot->resource_link, BotService::DEFAULT_HOST);
        self::assertEquals($bot->api_key, '');

        $dto = BotFactory::fromEntity($bot);
        $dto->api_key = config('services.key_activate.key');
        $bot = $this->botService->update($dto);
        self::assertEquals($bot->api_key, $dto->api_key);

        $user = $this->userService->getOrCreate($telegram_id = 398981226);

        $botDto = BotFactory::fromEntity($bot);
        $result = BottApi::checkUser(
            $telegram_id,
            $secret_key,
            $botDto->public_key,
            $botDto->private_key
        );

        $user = $this->userService->updateService($telegram_id, 'ig');

        $orderCreate = $this->orderService->create(
            $result['data'],
            BotFactory::fromEntity($bot),
            '0',
        );

        $order = Order::query()->where(['org_id' => $orderCreate['id']])->first();

        try {
            $orderData = $this->orderService->second(
                $result['data'],
                BotFactory::fromEntity($bot),
                $order,
            );
            self::assertEquals(1, 2);
        } catch (Exception $e) {
            self::assertEquals('Попытка установить несуществующий статус', $e->getMessage());
        }

        self::assertEquals($order->status, Order::STATUS_WAIT_RETRY); //по аналогии с сервисом
    }

    public function testOrder()
    {
        $this->countryService->getApiCountries();
        $bot = $this->botService->create(
            $public_key = '062d7c679ca22cf88b01b13c0b24b057',
            $private_key = 'd75bee5e605d87bf6ebd432a2b25eb0e',
            $bot_id = 123,
        );
        $secret_key = '29978beb742581e93e31ec12ac518b76299755483b9614b8';
        self::assertEquals($bot->public_key, $public_key);
        self::assertEquals($bot->private_key, $private_key);
        self::assertEquals($bot->bot_id, $bot_id);
        self::assertEquals($bot->resource_link, BotService::DEFAULT_HOST);
        self::assertEquals($bot->api_key, '');

        $dto = BotFactory::fromEntity($bot);
        $dto->api_key = config('services.key_activate.key');
        $bot = $this->botService->update($dto);
        self::assertEquals($bot->api_key, $dto->api_key);

        $user = $this->userService->getOrCreate($telegram_id = 398981226);

        $botDto = BotFactory::fromEntity($bot);
        $result = BottApi::checkUser(
            $telegram_id,
            $secret_key,
            $botDto->public_key,
            $botDto->private_key
        );

        $user = $this->userService->updateService($telegram_id, 'ig');

        $orderCreate = $this->orderService->create(
            $result['data'],
            BotFactory::fromEntity($bot),
            '0',
        );

        $order = Order::query()->where(['org_id' => $orderCreate['id']])->first();
        $order->status = Order::STATUS_CANCEL;

        $this->orderService->order(
            $result['data'],
            BotFactory::fromEntity($bot),
            $order,
        );
    }
}
