<?php

namespace App\Models\Order;

use App\Models\Country\Country;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = false;
    protected $table = 'order';

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
