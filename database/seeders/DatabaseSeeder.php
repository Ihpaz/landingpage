<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Cms\MenuSeeder;
use Database\Seeders\Cms\RolesAndPermissionsSeeder;
use Database\Seeders\Cms\UserSeeder;
use Database\Seeders\Master\LocationPermissionSeeder;
use Database\Seeders\Master\LocationSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Core Seeder
        // $this->call(RolesAndPermissionsSeeder::class);
        // $this->call(UserSeeder::class);

        // Location
        // $this->call(LocationSeeder::class);
        // $this->call(LocationPermissionSeeder::class);
        $this->call(MenuSeeder::class);
    }
}
