<?php

namespace App\Services\Activate\Order;

use App\Dto\BotDto;
use App\Models\Order\Order;
use App\Services\Activate\Order\Strategy\CustomCommentConcreteStrategy;
use App\Services\Activate\Order\Strategy\CustomCommentsPackageConcreteStrategy;
use App\Services\Activate\Order\Strategy\DefaultConcreteStrategy;
use App\Services\Activate\Order\Strategy\MentionsUserConcreteStrategy;
use App\Services\Activate\Order\Strategy\PackageConcreteStrategy;
use App\Services\Activate\Order\Strategy\PollConcreteStrategy;
use App\Services\Activate\Order\Strategy\SubscriptionsConcreteStrategy;
use Illuminate\Http\Request;

class OrderStrategy
{
    /**
     * @var OrderInterface
     */
    private $strategy;

    /**
     * @var BotDto
     */
    private BotDto $botDto;

    public function __construct($type, $botDto)
    {
        $this->botDto = $botDto;
        switch ($type) {
            case Order::DEFAULT_TYPE:
                $this->strategy = new DefaultConcreteStrategy($this->botDto);
                break;
            case Order::PACKAGE_TYPE:
                $this->strategy = new PackageConcreteStrategy($this->botDto);
                break;
            case Order::CUSTOM_COMMENTS_TYPE:
                $this->strategy = new CustomCommentConcreteStrategy($this->botDto);
                break;
            case Order::MENTIONS_USER_FOLLOWERS_TYPE:
                $this->strategy = new MentionsUserConcreteStrategy($this->botDto);
                break;
            case Order::CUSTOM_COMMENTS_PACKAGE_TYPE:
                $this->strategy = new CustomCommentsPackageConcreteStrategy($this->botDto);
                break;
            case Order::POLL_TYPE:
                $this->strategy = new PollConcreteStrategy($this->botDto);
                break;
            case Order::SUBSCRIPTIONS_TYPE:
                $this->strategy = new SubscriptionsConcreteStrategy($this->botDto);
                break;
            default:
                throw new \RuntimeException('Неверный тип товара');
        }
    }

    public function create(Request $request): array
    {
        return $this->strategy->create($request);
    }
}
