<?php

namespace App\Models\Social;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    use HasFactory;

    protected $guarded = false;
    protected $table = 'social';
}
