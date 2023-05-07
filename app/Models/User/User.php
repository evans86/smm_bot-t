<?php

namespace App\Models\User;

use App\Models\Country\Country;
use App\Models\Country\SmsOperator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const LANGUAGE_RU = 'ru';
    const LANGUAGE_ENG = 'eng';

    use HasFactory;

    protected $guarded = false;
    protected $table = 'user';
}
