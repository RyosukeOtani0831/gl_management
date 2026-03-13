<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BasicAuthUserSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            ['username' => 'smd', 'password' => 'aDnk5h+ezW6m'],
            ['username' => '76529348', 'password' => '#u9-q&uE%j-|'],
            ['username' => '62874539', 'password' => '7Q2~dJrgddQe'],
            ['username' => '74896352', 'password' => 'M#D!h~E+xU4B'],
            ['username' => '94675382', 'password' => 'D&G$ztJ7xrLv'],
            ['username' => '87259364', 'password' => '_C.qvH9Rrxk%'],
            ['username' => '23586794', 'password' => 'dS5cSUfXwn).'],
            ['username' => '62734859', 'password' => 'Js-BUN&~5+WR'],
            ['username' => '46523987', 'password' => 'eTG3xAzE2|Vt'],
            ['username' => '37984652', 'password' => 'KQC$v_d9EZq.'],
        ];

        foreach ($accounts as $account) {
            DB::table('basic_auth_users')->insert([
                'username' => $account['username'],
                'password' => Hash::make($account['password']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}