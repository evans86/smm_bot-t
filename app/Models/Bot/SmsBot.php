<?php

namespace App\Models\Bot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsBot extends Model
{
    use HasFactory;

    protected $guarded = false;
    protected $table = 'bot';
}
