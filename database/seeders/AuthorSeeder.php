<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\User\UserType;
use App\Enums\User\UserStatus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors = [
            [
                'username'   => 'nagufib_mahfouz',
                'first_name' => 'Naguib',
                'last_name'  => 'Mahfouz',
                'bio'        => 'Egyptian writer who won the 1988 Nobel Prize for Literature.',
                'country'    => 'Egypt'
            ],
            [
                'username'   => 'ghassfan_kanafani',
                'first_name' => 'Ghassan',
                'last_name'  => 'Kanafani',
                'bio'        => 'Palestinian author and a leading member of the PFLP.',
                'country'    => 'Palestine'
            ],
            [
                'username'   => 'elif_sfhafak',
                'first_name' => 'Elif',
                'last_name'  => 'Shafak',
                'bio'        => 'Turkish-British novelist and activist.',
                'country'    => 'Turkey'
            ]
        ];

        foreach ($authors as $data) {
            DB::transaction(function () use ($data) {
                $user = User::create([
                    'username'   => $data['username'],
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'],
                    'password'   => Hash::make('password123'),
                    'type'       => UserType::Author,
                    'status'     => UserStatus::Active,
                ]);
                $user->author()->create([
                    'bio'     => $data['bio'],
                    'country' => $data['country'],
                ]);

                $user->photo()->create([
                    'file_path'  => 'authors/avatars/default-author.png',
                    'file_name'  => 'default-author.png',
                    'mime_type'  => 'image/png',
                    'size'       => 1024,
                    'collection' => 'avatar',
                ]);
            });
        }
    }
}
