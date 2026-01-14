<?php

namespace App\Enums\User;
use App\Traits\EnumToArray;

enum UserType: string
{
    case Organizer = 'organizer';
    case Attendee = 'attendee';
    public function is(string $type): bool
    {
        return $this->value === $type;
    }

    public static function tryFromString(string $value): ?self
    {
        return collect(self::cases())->first(fn($case) => $case->value === $value);
    }
}
