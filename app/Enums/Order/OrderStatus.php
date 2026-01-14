<?php

namespace App\Enums\Order;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
}
