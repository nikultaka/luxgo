<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            [
            'role_id' => '1',
            'name' => "Luxgo's Admin",
            'email' => 'admin@gmail.com',
            'password' => '$2y$10$yB.PAVyVbz5.FT10Jyg0GO8NueTUXJEPd7yVqMkml9qWoFwv8KyUW',
            'status' => '1',
            'is_admin' => '1',
            'created_at' => '2021-06-10 05:53:43',
            'updated_at' => '2021-06-10 05:56:34',
            ]      
    ]);
    }
}
