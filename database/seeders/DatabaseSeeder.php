<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);

        \App\Models\User::factory()->create(
            [
                'name' => 'Frank Lisboa Abad',
                'email' => 'frank@admin.com',
                'email_verified_at' => now(),
                'password' => bcrypt('secret'), // password
            ]
        )->assignRole('Admin');

        User::factory()->count(10)->create();
    }
}
