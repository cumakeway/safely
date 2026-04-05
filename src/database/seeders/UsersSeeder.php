<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $managerId = DB::table('roles')->where('name', 'manager')->value('id');
        $workerId  = DB::table('roles')->where('name', 'worker')->value('id');

        DB::table('users')->insert([
            // ── Managers ──────────────────────────────────────────────
            [
                'name'              => 'Alice Manager',
                'email'             => 'alice@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => $managerId,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Bob Manager',
                'email'             => 'bob@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => $managerId,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],

            // ── Workers ───────────────────────────────────────────────
            [
                'name'              => 'Charlie Worker',
                'email'             => 'charlie@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => $workerId,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Diana Worker',
                'email'             => 'diana@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => $workerId,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'name'              => 'Eve Worker',
                'email'             => 'eve@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => $workerId,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}