<?php

namespace App\Services\Activate;

use App\Dto\BotDto;
use App\Models\Country\Country;
use App\Models\Order\Order;
use App\Models\Order\SmsOrder;
use App\Models\Proxy\Proxy;
use App\Models\User\User;
use App\Services\External\BottApi;
use App\Services\External\ProxyApi;
use App\Services\MainService;
use http\Exception\RuntimeException;

class ProxyService extends MainService
{
    /**
     * @param $count
     * @param $period
     * @param $country
     * @param $version
     * @param $type
     * @param array $userData
     * @param BotDto $botDto
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createOrder($count, $period, $country, $version, $type, $enter_amount, array $userData, BotDto $botDto)
    {
        $proxyApi = new ProxyApi($botDto->api_key);
//        $proxyApi = new ProxyApi(config('services.key_proxy.key'));

        $user = User::query()->where(['telegram_id' => $userData['user']['telegram_id']])->first();
        if (is_null($user)) {
            throw new RuntimeException('not found user');
        }

        if ($enter_amount > $userData['money']) {
            throw new \RuntimeException('Пополните баланс в боте');
        }

        $order = $proxyApi->buy($count, $period, $country, $version, $type);
        $list = $order['list'];
        $list = current($list);

        $country = Country::query()->where(['iso_two' => $order['country']])->first();
        $proxy = Proxy::query()->where(['version' => $order['version']])->first();

        $amountStart = intval(floatval($order['price']) * 100);
        $amountFinal = $amountStart + $amountStart * $botDto->percent / 100;

        //убрать потом
//        $amountStart = intval(floatval($order['price']) * 100);
//        $amountFinal = $amountStart + $amountStart * 10 / 100;

//        // Попытаться списать баланс у пользователя
        $result = BottApi::subtractBalance($botDto, $userData, $amountFinal, 'Списание баланса для прокси '
            . $list['ip']);

        $data = [
            'user_id' => $user->id,
            'bot_id' => $botDto->id,
            'user_org_id' => $order['user_id'],
            'balance_org' => $order['balance'],
            'order_org_id' => $order['order_id'],
            'count' => $order['count'],
            'price' => $amountFinal,
            'period' => $order['period'],
            'proxy_id' => $proxy->id,
            'type' => $order['type'],
            'country_id' => $country->id,
            'prolong_org_id' => $list['id'],
            'ip' => $list['ip'],
            'host' => $list['host'],
            'port' => $list['port'],
            'user' => $list['user'],
            'pass' => $list['pass'],
            'status_org' => $list['active'],
            'start_time' => $list['unixtime'],
            'end_time' => $list['unixtime_end'],
        ];

        $order = Order::create($data);

        $result = [
            'order_org_id' => $order->prolong_org_id,
            'proxy' => $order->proxy->version,
            'country' => [
                'org_id' => $order->country->iso_two,
                'name_ru' => $order->country->name_ru,
                'name_en' => $order->country->name_en,
                'image' => $order->country->image
            ],
            'price' => $order->price,
            'host' => $order->host,
            'port' => $order->port,
            'user' => $order->user,
            'pass' => $order->pass,
            'type' => $order->type,
            'ip' => $order->ip,
            'start_time' => $order->start_time,
            'end_time' => $order->end_time
        ];

        return $result;
    }

    /**
     * @param int $user_id
     * @param array $userData
     * @return array
     */
    public function getOrders(int $user_id, array $userData)
    {
        $user = User::query()->where(['telegram_id' => $userData['user']['telegram_id']])->first();
//        $user = User::query()->where(['telegram_id' => $user_id])->first();

        $proxies = Order::query()->where('status_org', 1)->where('user_id', $user->id)->get();

        $result = [];

        foreach ($proxies as $proxy) {
            array_push($result, [
                'order_org_id' => $proxy->prolong_org_id,
                'proxy' => $proxy->proxy->version,
                'country' => [
                    'org_id' => $proxy->country->iso_two,
                    'name_ru' => $proxy->country->name_ru,
                    'name_en' => $proxy->country->name_en,
                    'image' => $proxy->country->image
                ],
                'price' => $proxy->price,
                'host' => $proxy->host,
                'port' => $proxy->port,
                'user' => $proxy->user,
                'pass' => $proxy->pass,
                'type' => $proxy->type,
                'ip' => $proxy->ip,
                'start_time' => $proxy->start_time,
                'end_time' => $proxy->end_time
            ]);
        }

        return $result;
    }

    public function checkWork($order_org_id)
    {
        //        $proxyApi = new ProxyApi($botDto->api_key);
        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $status = $proxyApi->check($order_org_id);

        $result = $status['proxy_status'];

        return $result;
    }

    /**
     * @param $order_org_id
     * @param $type
     * @return bool|string
     */
    public function updateType($order_org_id, $type)
    {
        //        $proxyApi = new ProxyApi($botDto->api_key);
        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $result = $proxyApi->settype($order_org_id, $type);

        if ($result['status'] == 'no')
            throw new \RuntimeException($result['error']);

        return true;
    }

    /**
     * @param $order_org_id
     * @return mixed
     */
    public function deleteProxy($order_org_id)
    {
        //        $proxyApi = new ProxyApi($botDto->api_key);
        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $proxy = Order::query()->where(['prolong_org_id' => $order_org_id])->first();

        $result = $proxyApi->delete($order_org_id);

        if ($result['count'] != 1) {
            throw new \RuntimeException('Error delete in service');
        } else {
            $proxy->status_org = 0;
            $proxy->save();
            $result = true;
        }

        return true;
    }

    /**
     * @param $country
     * @param $version
     * @param BotDto|null $botDto
     * @return mixed
     */
    public function getCount($country, $version, BotDto $botDto)
    {
        $proxyApi = new ProxyApi($botDto->api_key);
//        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $count = $proxyApi->getcount($country, $version);

        return $count['count'];
    }

    /**
     * @param $count
     * @param $period
     * @param $version
     * @param BotDto|null $botDto
     * @return array
     */
    public function getPrice($count, $period, $version, BotDto $botDto)
    {
        $proxyApi = new ProxyApi($botDto->api_key);
//        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $price = $proxyApi->getprice($count, $period, $version);

        $amountStart = intval(floatval($price['price']) * 100);
        $amountFinal = $amountStart + $amountStart * $botDto->percent / 100;

        //убрать потом
//        $amountStart = intval(floatval($price['price']) * 100);
//        $amountFinal = $amountStart + $amountStart * 10 / 100;

        $result = [
            'price' => $amountFinal,
            'period' => $price['period'],
            'count' => $price['count'],
            'price_single' => $price['price_single'],
        ];

        return $result;
    }

    /**
     * @param BotDto|null $botDto
     * @return array
     */
    public function formingProxy(BotDto $botDto)
    {
        $proxyApi = new ProxyApi($botDto->api_key);
//        $proxyApi = new ProxyApi(config('services.key_proxy.key'));
        $proxies = Proxy::all();

        $result = [];
        foreach ($proxies as $key => $proxy) {

            $countries = $proxyApi->getcountry($proxy->version);
            $countries = $countries['list'];

            $countriesArr = [];
            foreach ($countries as $country) {

                try {
                    $countryProxy = Country::query()->where(['iso_two' => $country])->first();

                    array_push($countriesArr, [
                        'org_id' => $countryProxy->iso_two,
                        'name_ru' => $countryProxy->name_ru,
                        'name_en' => $countryProxy->name_en,
                        'image' => $countryProxy->image
                    ]);
                } catch (\Exception $e) {
                    continue;
                }
            }

            array_push($result, [
                'title' => $proxy->title,
                'version' => $proxy->version,
                'countries' => $countriesArr
            ]);
        }

        return $result;
    }
}
