<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('roles')->insert([
            ['name' => 'manager', 'label' => 'Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'worker',  'label' => 'Worker',  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
