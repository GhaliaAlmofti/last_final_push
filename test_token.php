<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('type', 'admin')->first();

if ($user) {
    $token = $user->createToken('test')->plainTextToken;
    echo "Token: $token\n";
} else {
    echo "No admin user found\n";
}