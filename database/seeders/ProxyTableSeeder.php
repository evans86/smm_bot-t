<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ProxyTableSeeder extends Seeder
{
    public function run()
    {
        $data[] = [
            "version" => "3",
            "title" => "IPv4 Shared"
        ];
        $data[] = [
            "version" => "4",
            "title" => "IPv4"
        ];
        $data[] = [
            "version" => "6",
            "title" => "IPv6"
        ];


        DB::table('proxy')->insert($data);
    }
}
