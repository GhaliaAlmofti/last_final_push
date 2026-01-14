<?php

namespace Database\Seeders;
use App\Enums\User\UserStatus;
use App\Enums\User\UserType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class adminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            User::create([
            'username'=> 'admin',
            'first_name'=> 'admin',
            'last_name'=> 'user',
            'password'=>'password' , 
            'type'=> UserType::Admin->value, 
            'status'=>UserStatus::Active->value
        ]);
    }
}
