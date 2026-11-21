<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'uuid' => 'fdfd9562-a3c4-4d89-9a25-83e9b14f85e1',
                'name' => 'Administrator',
                'email' => 'reza.f@lintec.co.id',
                'email_verified_at' => '2026-10-12 23:04:46',
                'password' => '$2y$12$xnMNtyPuaZAfma2hs33.Oe3mwGdur9x6asnr2XvC8VvrCmYitRJRG',
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'is_active' => 1,
                'last_login_at' => '2026-10-18 21:21:55',
                'last_login_ip' => '127.0.0.1',
                'remember_token' => null,
                'created_at' => '2026-10-12 23:04:46',
                'updated_at' => '2026-10-18 21:21:55',
            ]
        ]);
    }
}
