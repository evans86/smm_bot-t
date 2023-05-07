<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    public function run()
    {
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/b/b9/Flag_of_Australia.svg/22px-Flag_of_Australia.svg.png",
            "name_ru" => "Австралия",
            "name_en" => "Australia",
            "iso_two" => "au"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/4/41/Flag_of_Austria.svg/22px-Flag_of_Austria.svg.png",
            "name_ru" => "Австрия",
            "name_en" => "Austria",
            "iso_two" => "at"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Flag_of_Bangladesh.svg/22px-Flag_of_Bangladesh.svg.png",
            "name_ru" => "Бангладеш",
            "name_en" => "Bangladesh",
            "iso_two" => "bd"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/8/85/Flag_of_Belarus.svg/22px-Flag_of_Belarus.svg.png",
            "name_ru" => "Белоруссия",
            "name_en" => "Belarus",
            "iso_two" => "by"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/9/92/Flag_of_Belgium_%28civil%29.svg/22px-Flag_of_Belgium_%28civil%29.svg.png",
            "name_ru" => "Бельгия",
            "name_en" => "Belgium",
            "iso_two" => "be"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Flag_of_Bulgaria.svg/22px-Flag_of_Bulgaria.svg.png",
            "name_ru" => "Болгария",
            "name_en" => "Bulgaria",
            "iso_two" => "bg"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Flag_of_Canada.svg/22px-Flag_of_Canada.svg.png",
            "name_ru" => "Канада",
            "name_en" => "Canada",
            "iso_two" => "ca",
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/7/78/Flag_of_Chile.svg/22px-Flag_of_Chile.svg.png",
            "name_ru" => "Чили",
            "name_en" => "Chile",
            "iso_two" => "cl"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Flag_of_the_People%27s_Republic_of_China.svg/22px-Flag_of_the_People%27s_Republic_of_China.svg.png",
            "name_ru" => "КНР",
            "name_en" => "China",
            "iso_two" => "cn",
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Flag_of_Cyprus.svg/22px-Flag_of_Cyprus.svg.png",
            "name_ru" => "Кипр",
            "name_en" => "Cyprus",
            "iso_two" => "cy"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Flag_of_the_Czech_Republic.svg/22px-Flag_of_the_Czech_Republic.svg.png",
            "name_ru" => "Чехия",
            "name_en" => "Czech",
            "iso_two" => "cz"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Flag_of_Denmark.svg/22px-Flag_of_Denmark.svg.png",
            "name_ru" => "Дания",
            "name_en" => "Denmark",
            "iso_two" => "dk"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Flag_of_Egypt.svg/22px-Flag_of_Egypt.svg.png",
            "name_ru" => "Египет",
            "name_en" => "Egypt",
            "iso_two" => "eg"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/8/8f/Flag_of_Estonia.svg/22px-Flag_of_Estonia.svg.png",
            "name_ru" => "Эстония",
            "name_en" => "Estonia",
            "iso_two" => "ee"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Flag_of_Finland.svg/22px-Flag_of_Finland.svg.png",
            "name_ru" => "Финляндия",
            "name_en" => "Finland",
            "iso_two" => "fi"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Flag_of_France.svg/22px-Flag_of_France.svg.png",
            "name_ru" => "Франция",
            "name_en" => "France",
            "iso_two" => "fr"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/0/0f/Flag_of_Georgia.svg/22px-Flag_of_Georgia.svg.png",
            "name_ru" => "Грузия",
            "name_en" => "Georgia",
            "iso_two" => "ge"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Flag_of_Germany.svg/22px-Flag_of_Germany.svg.png",
            "name_ru" => "Германия",
            "name_en" => "Germany",
            "iso_two" => "de"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Flag_of_Greece.svg/22px-Flag_of_Greece.svg.png",
            "name_ru" => "Греция",
            "name_en" => "Greece",
            "iso_two" => "gr"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/5/5b/Flag_of_Hong_Kong.svg/22px-Flag_of_Hong_Kong.svg.png",
            "name_ru" => "Гонконг",
            "name_en" => "HongKong",
            "iso_two" => "hk"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/4/41/Flag_of_India.svg/22px-Flag_of_India.svg.png",
            "name_ru" => "Индия",
            "name_en" => "India",
            "iso_two" => "in"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/22px-Flag_of_Indonesia.svg.png",
            "name_ru" => "Индонезия",
            "name_en" => "Indonesia",
            "iso_two" => "id"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/4/45/Flag_of_Ireland.svg/22px-Flag_of_Ireland.svg.png",
            "name_ru" => "Ирландия",
            "name_en" => "Ireland",
            "iso_two" => "ie"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Flag_of_Israel.svg/22px-Flag_of_Israel.svg.png",
            "name_ru" => "Израиль",
            "name_en" => "Israel",
            "iso_two" => "il"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/0/03/Flag_of_Italy.svg/22px-Flag_of_Italy.svg.png",
            "name_ru" => "Италия",
            "name_en" => "Italy",
            "iso_two" => "it"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Flag_of_Japan.svg/22px-Flag_of_Japan.svg.png",
            "name_ru" => "Япония",
            "name_en" => "Japan",
            "iso_two" => "jp"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/d/d3/Flag_of_Kazakhstan.svg/22px-Flag_of_Kazakhstan.svg.png",
            "name_ru" => "Казахстан",
            "name_en" => "Kazakhstan",
            "iso_two" => "kz"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/c/c7/Flag_of_Kyrgyzstan.svg/22px-Flag_of_Kyrgyzstan.svg.png",
            "name_ru" => "Киргизия",
            "name_en" => "Kyrgyzstan",
            "iso_two" => "kg"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/8/84/Flag_of_Latvia.svg/22px-Flag_of_Latvia.svg.png",
            "name_ru" => "Латвия",
            "name_en" => "Latvia",
            "iso_two" => "lv"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/1/11/Flag_of_Lithuania.svg/22px-Flag_of_Lithuania.svg.png",
            "name_ru" => "Литва",
            "name_en" => "Lithuania",
            "iso_two" => "lt"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/6/66/Flag_of_Malaysia.svg/22px-Flag_of_Malaysia.svg.png",
            "name_ru" => "Малайзия",
            "name_en" => "Malaysia",
            "iso_two" => "my"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Flag_of_Mexico.svg/22px-Flag_of_Mexico.svg.png",
            "name_ru" => "Мексика",
            "name_en" => "Mexico",
            "iso_two" => "mx"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/2/27/Flag_of_Moldova.svg/22px-Flag_of_Moldova.svg.png",
            "name_ru" => "Молдавия",
            "name_en" => "Moldova",
            "iso_two" => "md"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/2/20/Flag_of_the_Netherlands.svg/22px-Flag_of_the_Netherlands.svg.png",
            "name_ru" => "Нидерланды",
            "name_en" => "Netherlands",
            "iso_two" => "nl"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/7/79/Flag_of_Nigeria.svg/22px-Flag_of_Nigeria.svg.png",
            "name_ru" => "Нигерия",
            "name_en" => "Nigeria",
            "iso_two" => "ng"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Flag_of_Norway.svg/22px-Flag_of_Norway.svg.png",
            "name_ru" => "Норвегия",
            "name_en" => "Norway",
            "iso_two" => "no"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/9/99/Flag_of_the_Philippines.svg/22px-Flag_of_the_Philippines.svg.png",
            "name_ru" => "Филиппины",
            "name_en" => "Philippines",
            "iso_two" => "ph"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/1/12/Flag_of_Poland.svg/22px-Flag_of_Poland.svg.png",
            "name_ru" => "Польша",
            "name_en" => "Poland",
            "iso_two" => "pl"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Flag_of_Portugal.svg/22px-Flag_of_Portugal.svg.png",
            "name_ru" => "Португалия",
            "name_en" => "Portugal",
            "iso_two" => "pt"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/7/73/Flag_of_Romania.svg/22px-Flag_of_Romania.svg.png",
            "name_ru" => "Румыния",
            "name_en" => "Romania",
            "iso_two" => "ro"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Flag_of_Russia.svg/22px-Flag_of_Russia.svg.png",
            "name_ru" => "Россия",
            "name_en" => "Russia",
            "iso_two" => "ru",
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/f/ff/Flag_of_Serbia.svg/22px-Flag_of_Serbia.svg.png",
            "name_ru" => "Сербия",
            "name_en" => "Serbia",
            "iso_two" => "rs"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/4/48/Flag_of_Singapore.svg/22px-Flag_of_Singapore.svg.png",
            "name_ru" => "Сингапур",
            "name_en" => "Singapore",
            "iso_two" => "sg"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/f/f0/Flag_of_Slovenia.svg/22px-Flag_of_Slovenia.svg.png",
            "name_ru" => "Словения",
            "name_en" => "Slovenia",
            "iso_two" => "si"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/a/af/Flag_of_South_Africa.svg/22px-Flag_of_South_Africa.svg.png",
            "name_ru" => "ЮАР",
            "name_en" => "South Africa",
            "iso_two" => "za"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/0/09/Flag_of_South_Korea.svg/22px-Flag_of_South_Korea.svg.png",
            "name_ru" => "Республика Корея",
            "name_en" => "South Korea",
            "iso_two" => "kr"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Flag_of_Spain.svg/22px-Flag_of_Spain.svg.png",
            "name_ru" => "Испания",
            "name_en" => "Spain",
            "iso_two" => "es"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Flag_of_Sweden.svg/22px-Flag_of_Sweden.svg.png",
            "name_ru" => "Швеция",
            "name_en" => "Sweden",
            "iso_two" => "se"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Flag_of_Switzerland.svg/20px-Flag_of_Switzerland.svg.png",
            "name_ru" => "Швейцария",
            "name_en" => "Switzerland",
            "iso_two" => "ch"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/7/72/Flag_of_the_Republic_of_China.svg/22px-Flag_of_the_Republic_of_China.svg.png",
            "name_ru" => "Тайвань",
            "name_en" => "Taiwan",
            "iso_two" => "tw"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Flag_of_Tajikistan.svg/22px-Flag_of_Tajikistan.svg.png",
            "name_ru" => "Таджикистан",
            "name_en" => "Tajikistan",
            "iso_two" => "tj"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/b/b4/Flag_of_Turkey.svg/22px-Flag_of_Turkey.svg.png",
            "name_ru" => "Турция",
            "name_en" => "Turkey",
            "iso_two" => "tr"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/4/49/Flag_of_Ukraine.svg/22px-Flag_of_Ukraine.svg.png",
            "name_ru" => "Украина",
            "name_en" => "Ukraine",
            "iso_two" => "ua"
        ];
        $data[] = [
            "image" => "https://upload.wikimedia.org/wikipedia/commons/8/83/Flag_of_the_United_Kingdom_%283-5%29.svg?uselang=ru",
            "name_ru" => "Великобритания",
            "name_en" => "United Kingdom",
            "iso_two" => "gb",
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/8/84/Flag_of_Uzbekistan.svg/22px-Flag_of_Uzbekistan.svg.png",
            "name_ru" => "Узбекистан",
            "name_en" => "Uzbekistan",
            "iso_two" => "uz"
        ];
        $data[] = [
            "image" => "//upload.wikimedia.org/wikipedia/commons/thumb/2/21/Flag_of_Vietnam.svg/22px-Flag_of_Vietnam.svg.png",
            "name_ru" => "Вьетнам",
            "name_en" => "Vietnam",
            "iso_two" => "vn"
        ];
        $data[] = [
            "image" => "https://upload.wikimedia.org/wikipedia/commons/archive/c/cb/20060205124603%21Flag_of_the_United_Arab_Emirates.svg",
            "name_ru" => "ОАЭ",
            "name_en" => "United Arab Emirates",
            "iso_two" => "ae"
        ];
        $data[] = [
            "image" => "https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg",
            "name_ru" => "США",
            "name_en" => "United States",
            "iso_two" => "us"
        ];

        DB::table('country')->insert($data);
    }


}
