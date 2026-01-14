<?php

namespace App\Policies;

use App\Enums\User\UserType;
use App\Models\Book\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    /**
     * Admin Bypass: Admins can do anything.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->type === UserType::Admin) {
            return true;
        }
        return null; // Continue to specific checks
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // We want all authenticated users to browse the catalog
        return $user->status === 'active'; 
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Book $book): bool
    {
        // 1. If Customer, they can view all books
        if ($user->type === UserType::Customer) {
            return true;
        }

        // 2. If Author, check if they own this book
        if ($user->type === UserType::Author) {
            return $book->authors()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->type === UserType::Author;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Book $book): bool
    {
        // Only an Author who owns the book can update it
        return $user->type === UserType::Author && 
               $book->authors()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Book $book): bool
    {
        return $this->update($user, $book);
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Book $book): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Book $book): bool
    {
        return false;
    }
}
