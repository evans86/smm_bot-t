<?php

namespace App\Models\User;

use App\Models\Activate\SmsCountry;
use App\Models\Activate\SmsOperator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsUser extends Model
{
    const LANGUAGE_RU = 'ru';
    const LANGUAGE_ENG = 'eng';

    use HasFactory;

    protected $guarded = false;
    protected $table = 'user';

    public function country()
    {
        return $this->hasOne(SmsCountry::class, 'id', 'country_id');
    }
}
