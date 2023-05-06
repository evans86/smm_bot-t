<?php

namespace Tests\Unit;

use App\Services\Activate\OrderService;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @var OrderService
     */
    private OrderService $orderService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->orderService = new OrderService();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function testService()
    {
        $this->assertTrue($this->orderService->test());
    }

}
