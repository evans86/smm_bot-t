<?php

namespace App\Services\Activate\Order\Strategy;

use App\Dto\BotDto;
use App\Helpers\OrdersHelper;
use App\Services\Activate\Order\OrderInterface;
use App\Services\External\PartnerApi;
use Illuminate\Http\Request;

class PollConcreteStrategy extends MainConcreteStrategy implements OrderInterface
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
        if (is_null($request->answer_number)) //необходимое количество
            throw new \RuntimeException('Not Found Params: answer_number');

        $type_id = $request->type_id;
        $link = $request->link;
        $quantity = $request->quantity;
        $answer_number = $request->answer_number;

        $partnerApi = new PartnerApi($this->botDto->api_key);

        $order = $partnerApi->add(
            $type_id, //id товара в ресрусе
            $link, //ссылка на ресурс
            $quantity, //необходимое количество
            null,
            null,
            $answer_number
        );

        if(isset($order['error']))
            throw new \RuntimeException(OrdersHelper::requestArray($order['error']));

        return $order;
    }
}
