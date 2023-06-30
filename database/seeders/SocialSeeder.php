<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialSeeder extends Seeder
{
    public function run()
    {
        $data[] = [
            'id' => 1,
            'name_en' => 'Telegram',
            'name_ru' => 'Телеграм',
            'short_name' => 'TG',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/8/83/Telegram_2019_Logo.svg',
        ];
        $data[] = [
            'id' => 2,
            'name_en' => 'YouTube',
            'name_ru' => '',
            'short_name' => 'YT',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/YouTube_full-color_icon_%282017%29.svg/159px-YouTube_full-color_icon_%282017%29.svg.png',
        ];
        $data[] = [
            'id' => 3,
            'name_en' => 'Discord',
            'name_ru' => 'Дискорд',
            'short_name' => 'DC',
            'image' => 'https://upload.wikimedia.org/wikipedia/fr/4/4f/Discord_Logo_sans_texte.svg',
        ];
        $data[] = [
            'id' => 4,
            'name_en' => 'Twitch',
            'name_ru' => 'Твитч',
            'short_name' => '',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/5/58/Twitch_font_awesome.svg',
        ];
        $data[] = [
            'id' => 5,
            'name_en' => 'Instagram',
            'name_ru' => 'Инстаграм',
            'short_name' => 'IG',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/9/95/Instagram_logo_2022.svg',
        ];
        $data[] = [
            'id' => 6,
            'name_en' => 'VK.com',
            'name_ru' => 'ВК',
            'short_name' => 'VK',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/f/f3/VK_Compact_Logo_%282021-present%29.svg',
        ];
        $data[] = [
            'id' => 7,
            'name_en' => 'Spotify',
            'name_ru' => 'Спотифай',
            'short_name' => 'S',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/1/19/Spotify_logo_without_text.svg',
        ];
        $data[] = [
            'id' => 8,
            'name_en' => 'Web Traffic',
            'name_ru' => 'Веб Трафик',
            'short_name' => '',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/e/e0/Web-icon-voltrans.png',
        ];
        $data[] = [
            'id' => 9,
            'name_en' => 'Facebook',
            'name_ru' => 'Фейсбук',
            'short_name' => 'fB',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg',
        ];
        $data[] = [
            'id' => 10,
            'name_en' => 'TikTok',
            'name_ru' => 'ТикТок',
            'short_name' => '',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/e/ed/Tiktok_logo_black.svg',
        ];
        $data[] = [
            'id' => 11,
            'name_en' => 'Twitter',
            'name_ru' => 'Твиттер',
            'short_name' => 'TW',
            'image' => 'https://upload.wikimedia.org/wikipedia/commons/6/6f/Logo_of_Twitter.svg',
        ];

        DB::table('social')->insert($data);
    }
}
