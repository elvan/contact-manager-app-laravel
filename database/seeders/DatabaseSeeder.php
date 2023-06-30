<?php

namespace Database\Seeders;

use Database\Seeders\CompanySeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CompanySeeder::class,
            ContactSeeder::class,
        ]);
    }
}
