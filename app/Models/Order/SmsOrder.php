<?php

namespace App\Models\Order;

use App\Models\Activate\SmsCountry;
use App\Models\User\SmsUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsOrder extends Model
{
    const ACCESS_RETRY_GET = 1; //Готовность номера подтверждена
    const NO_NUMBERS = 2; //Нет свободных номеров для текущего сервиса
    const ACCESS_READY = 3; //Ожидание нового смс
    const STATUS_WAIT_CODE = 4; //Ожидание первой смс
    const STATUS_WAIT_RETRY = 5; //Ожидание уточнения кода
    const ACCESS_ACTIVATION = 6; //Сервис успешно активирован
    const STATUS_OK = 7; //Статус ОК
    const ACCESS_CANCEL = 8; //Отмена активации
    const STATUS_CANCEL = 9; //Активация/аренда отменена
    const STATUS_FINISH = 10; //Активация/аренда успешно завершена

    use HasFactory;

    protected $guarded = false;
    protected $table = 'order';

    public function user()
    {
        return $this->hasOne(SmsUser::class, 'id', 'user_id');
    }

    public function country()
    {
        return $this->hasOne(SmsCountry::class, 'id', 'country_id');
    }
}
