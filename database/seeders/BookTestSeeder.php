<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book\Book;
use App\Models\Book\Category;
use App\Models\UsersType\Author;
use App\Enums\User\UserType;
use App\Enums\User\UserStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BookTestSeeder extends Seeder
{
    public function run(): void
    {
        $catProgramming = Category::firstOrCreate(
            ['name' => 'Programming'],
            ['slug' => 'programming']
        );
        $catDesign = Category::firstOrCreate(
            ['name' => 'Design'],
            ['slug' => 'design']
        );

        // $user1 = User::firstOrCreate(
        //     ['username' => 'ahmed_dev'],
        //     [
        //         'name'     => 'Ahmed Mohamed',
        //         'password' => Hash::make('password123'),
        //         'type'     => UserType::Author,
        //         'status'   => UserStatus::Active,
        //     ]
        // );

        // $author1 = Author::firstOrCreate(
        //     ['user_id' => $user1->id],
        //     ['bio' => 'Senior Laravel Developer', 'country' => 'Libya']
        // );

        // $user2 = User::firstOrCreate(
        //     ['username' => 'sara_engineer'],
        //     [
        //         'name'     => 'Sara Ali',
        //         'password' => Hash::make('password123'),
        //         'type'     => UserType::Author,
        //         'status'   => UserStatus::Active,
        //     ]
        // );

        // $author2 = Author::firstOrCreate(
        //     ['user_id' => $user2->id],
        //     ['bio' => 'Software Architect', 'country' => 'Libya']
        // );

        // $book = Book::firstOrCreate(
        //     ['ISBN' => '978-1234567890'],
        //     [
        //         'title'        => 'Advanced Laravel Architecture',
        //         'category_id'  => $catProgramming->id,
        //         'price'        => 99.99,
        //         'publish_year' => 2025,
        //         'stock'        => 10,
        //     ]
        // );

        // $book->authors()->sync([
        //     $author1->user_id => ['is_owner' => true],
        //     $author2->user_id => ['is_owner' => false],
        // ]);
    }
}
