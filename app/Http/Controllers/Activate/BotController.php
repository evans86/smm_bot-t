<?php

namespace App\Http\Controllers\Activate;

use App\Models\Bot\Bot;

class BotController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $bots = Bot::orderBy('id', 'DESC')->Paginate(10);

        $newBots = count(Bot::query()->where('created_at', '>', '2025-02-10 20:21:56')->get());
        $allCount = count(Bot::get());

        return view('activate.bot.index', compact(
            'bots',
            'allCount',
            'newBots'
        ));
    }
}
