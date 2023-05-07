<?php

namespace App\Models\Proxy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    use HasFactory;

    protected $guarded = false;
    protected $table = 'proxy';
}
