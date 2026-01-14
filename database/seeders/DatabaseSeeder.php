<?php

namespace Database\Seeders;

use App\Enums\User\UserStatus;
use App\Enums\User\UserType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            adminUserSeeder::class,
            AuthorSeeder::class,
            BookTestSeeder::class,
        ]);
    }
}
