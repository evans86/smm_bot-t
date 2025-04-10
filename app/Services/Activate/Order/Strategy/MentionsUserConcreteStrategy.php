<?php

namespace App\Services\Activate\Order\Strategy;

use App\Dto\BotDto;
use App\Helpers\OrdersHelper;
use App\Services\Activate\Order\OrderInterface;
use App\Services\External\PartnerApi;
use Illuminate\Http\Request;

class MentionsUserConcreteStrategy extends MainConcreteStrategy implements OrderInterface
{
    public function __construct(BotDto $botDto)
    {
        parent::__construct($botDto);
    }

    public function create(Request $request): array
    {
        if (is_null($request->type_id))
            throw new \RuntimeException('Not Found Params: type_id');
        if (is_null($request->link))
            throw new \RuntimeException('Not Found Params: link');
        if (is_null($request->quantity)) //необходимое количество
            throw new \RuntimeException('Not Found Params: quantity');
        if (is_null($request->username)) //username
            throw new \RuntimeException('Not Found Params: username');

        $type_id = $request->type_id;
        $link = $request->link;
        $quantity = $request->quantity;
        $username = $request->username;

        $partnerApi = new PartnerApi($this->botDto->getEncryptedApiKey());

        $order = $partnerApi->add(
            $type_id, //id товара в ресрусе
            $link, //ссылка на ресурс
            $quantity, //необходимое количество
            null,
            $username
        );

        if (isset($order['error']))
            throw new \RuntimeException(OrdersHelper::requestArray($order['error']));

        return $order;
    }
}
