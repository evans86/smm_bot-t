<?php

namespace App\Http\Controllers\Activate;

use App\Http\Controllers\Controller;
use App\Models\Social\Social;

class SocialController
{
    public function index()
    {
        $socials = Social::paginate(15);

        return view('activate.social.index', compact(
            'socials',
        ));
    }
}
