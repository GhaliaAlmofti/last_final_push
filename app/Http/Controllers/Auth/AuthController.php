<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Author\User\AuthorResource; // ✅ Add your AuthorResource
use App\Enums\User\UserStatus;
use App\Http\Resources\Owner\User\OwnerDetailsResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login($request)
    {
        $validatedData = $request->validated();

        $user = User::where('username', $validatedData['username'])->first();

        if (! $user || ! Hash::check($validatedData['password'], $user->password)) {
            return $this->unauthorized('Invalid credentials'); 
        }

        return $this->generateTokenResponse($user, 'Login successful');
    }

    public function refresh(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $currentToken = $user->currentAccessToken();

        if (! $currentToken->can('issue-access-token')) {
            return $this->unauthorized('Invalid token purpose.'); 
        }

        return DB::transaction(function () use ($user, $currentToken) {
            $currentToken->delete();
            return $this->generateTokenResponse($user, 'Token refreshed successfully');
        });
    }

    protected function generateTokenResponse(User $user, string $message)
    {
        $accessToken = $user->createToken('access_token', ['access-api', $user->type->value]);
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token']);
        $refreshToken->accessToken->update([
            'expires_at' => now()->addDays(30)
        ]);

        return $this->sendResponse([
            'user' => OwnerDetailsResource::make($user->load(['photo', 'owner'])),
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') * 60,
        ], $message);
    }

    public function logout()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user?->currentAccessToken()?->delete();

        return $this->sendResponse(null, 'Successfully logged out.');
    }
}
