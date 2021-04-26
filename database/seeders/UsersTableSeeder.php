<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'fullname' => Str::random(10),
                'cpf_cnpj' => '15680836025',
                'type' => 'comum',
                'email' => Str::random(10).'@gmail.com',
                'password' => Hash::make('password'),
                'wallet_balance' => 400.56
            ],
            [
                'fullname' => Str::random(10),
                'cpf_cnpj' => '42511258000100',
                'type' => 'lojista',
                'email' => Str::random(10).'@gmail.com',
                'password' => Hash::make('password'),
                'wallet_balance' => 4852.25
            ]
        ]);
    }
}
