<?php

namespace App\Enums\User;

enum UserStatus: string
{
    case Active = 'active';
    case Blocked = 'blocked';
    case Pending = 'pending';
}
