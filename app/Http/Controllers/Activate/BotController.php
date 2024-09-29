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

        $newBots = count(Bot::query()->where('created_at', '>', '2024-09-10 20:10:35')->get());
        $allCount = count(Bot::get());

        return view('activate.bot.index', compact(
            'bots',
            'allCount',
            'newBots'
        ));
    }
}
