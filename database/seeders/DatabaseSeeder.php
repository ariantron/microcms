<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->info('Seeding is disabled in production environment.');
            return;
        }
        $this->call([
            UserSeeder::class,
            PostSeeder::class,
        ]);
    }
}
