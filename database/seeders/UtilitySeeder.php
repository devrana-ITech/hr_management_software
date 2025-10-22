<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UtilitySeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //Default Settings
        DB::table('settings')->insert([
            [
                'name'  => 'mail_type',
                'value' => 'smtp',
            ],
            [
                'name'  => 'backend_direction',
                'value' => 'ltr',
            ],
            [
                'name'  => 'email_verification',
                'value' => 0,
            ],
            [
                'name'  => 'language',
                'value' => 'English---us',
            ],
            [
                'name'  => 'currency',
                'value' => 'USD',
            ],
        ]);

    }
}
