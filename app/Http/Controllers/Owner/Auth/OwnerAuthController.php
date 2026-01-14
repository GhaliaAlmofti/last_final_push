<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Author\User\RegisterAuthorRequest;
use App\Http\Requests\Author\User\RegisterOwnerRequest;
use App\Http\Requests\Author\User\UpdateOwnerMeRequest;
use App\Http\Resources\Owner\User\OwnerResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OwnerAuthController extends Controller
{
    /**
     * Owner authentication endpoints
     *
     * APIs for owners to register, login, refresh tokens, view profile and logout.
     *
     * @group Owner Auth
     */
    /**
     * Login event owner 
     */

    public function login(LoginRequest $request)
    {
        /**
         * Login (Owner)
         *
         * Authenticate an owner and return access + refresh tokens.
         *
         * @bodyParam username string required The owner's username. Example: owner1
         * @bodyParam password string required The owner's password. Example: secret123
         *
         * @response 200 {
         *  "user": {"id":1, "username":"owner1"},
         *  "access_token":"token...",
         *  "refresh_token":"token...",
         *  "token_type":"Bearer",
         *  "expires_in":86400
         * }
         * @response 401 {
         *  "message": "Invalid credentials"
         * }
         */
        $validatedData = $request->validated();

        $user = User::where('username', $validatedData['username'])
            ->where('type', UserType::Organizer)
            ->with(['owner', 'photo'])
            ->first();


        if (! $user || ! Hash::check($validatedData['password'], $user->password)) {
            return $this->unauthorized('Invalid credentials');
        }

        return $this->generateTokenResponse($user, 'Login successful');
    }


    /**
     * Refresh tokens
     */
    public function refresh(Request $request)
    {
        /**
         * Refresh tokens
         *
         * Issues a new access token by using the current refresh token. The
         * current token must have the `issue-access-token` ability.
         *
         * @authenticated
         * @header Authorization Bearer {token}
         * @response 200 {
         *  "user": {"id":1, "username":"owner1"},
         *  "access_token":"new_access_token...",
         *  "refresh_token":"new_refresh_token...",
         *  "token_type":"Bearer",
         *  "expires_in":86400
         * }
         * @response 401 {
         *  "message":"Invalid token purpose."
         * }
         */
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

    /**
     * Generate token response 
     */
    protected function generateTokenResponse(User $user, string $message, int $status = 200)
    {
        $accessToken = $user->createToken('access_token', ['access-api', $user->type->value]);
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token']);
        $refreshToken->accessToken->update([
            'expires_at' => now()->addDays(30)
        ]);

        return $this->sendResponse([
            'user'          =>  OwnerResource::make($user),
            'access_token'  => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type'    => 'Bearer',
            'expires_in'    => config('sanctum.expiration', 24) * 60 * 60,
        ], $message, [], $status);
    }
    /**
     * Logout 
     */
    public function logout()
    {
        /**
         * Logout (Owner)
         *
         * Revoke the current access token.
         *
         * @authenticated
         * @header Authorization Bearer {token}
         * @response 200 {"message":"Successfully logged out."}
         */
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user?->currentAccessToken()?->delete();

        return $this->sendResponse(null, 'Successfully logged out.');
    }
    /**
     * Register new event owner  
     */
    public function register(RegisterOwnerRequest $request)
    {
        /**
         * Register new owner
         *
         * Create a new owner account and return tokens.
         *
         * @bodyParam username string required Owner username. Example: owner1
         * @bodyParam first_name string required Owner first name. Example: Alice
         * @bodyParam last_name string required Owner last name. Example: Smith
         * @bodyParam password string required Password. Example: secret123
         * @bodyParam bio string optional Short bio.
         * @bodyParam country string optional Country name. Example: Egypt
         * @bodyParam photo file optional Avatar image file.
         *
         * @response 201 {
         *  "user": {"id":1, "username":"owner1"},
         *  "access_token":"token...",
         *  "refresh_token":"token...",
         *  "token_type":"Bearer",
         *  "expires_in":86400
         * }
         */
        return DB::transaction(function () use ($request) {
            $validatedData = $request->validated();

            $user = User::create([
                'username'   => $validatedData['username'],
                'first_name' => $validatedData['first_name'],
                'last_name'  => $validatedData['last_name'],
                'password'   => $validatedData['password'],
                'type'       => UserType::Organizer,
            ]);

            $user->owner()->create([
                'bio'     => $validatedData['bio'] ?? null,
                'country' => $validatedData['country'] ?? null,
            ]);

            if ($request->hasFile('photo')) {
                $user->uploadMedia($request->file('photo'), 'avatar');
            }

            $user->load(['owner', 'photo']);

            return $this->generateTokenResponse($user, 'Owner account created successfully.', 201);
        });
    }
    /**
     * Get my profile
     */
    public function me()
    {
        $user = Auth::user()->load(['owner', 'photo']);
        return $this->sendResponse(
            new OwnerResource($user),
            'User profile retrieved successfully.'
        );
    }
 /**
 * Update owner profile ✅
 * 
 * Update authenticated owner's profile information including photo.
 *
 * @authenticated
 * @header Authorization Bearer {access_token}
 * @group Owner Auth
 * 
 * @bodyParam username string optional Update username. Example: newowner1
 * @bodyParam first_name string optional First name. Example: New Alice
 * @bodyParam last_name string optional Last name. Example: New Smith  
 * @bodyParam bio string optional Owner bio.
 * @bodyParam country string optional Country. Example: UAE
 * @bodyParam photo file optional New avatar image.
 * 
 * @response 200 {
 *   "success": true,
 *   "message": "Profile updated successfully.",
 *   "data": {
 *     "user": {...},
 *     "owner": {...}
 *   }
 * }
 * @response 403 {
 *   "message": "Access denied. Organizer role required."
 * }
 * @response 422 {
 *   "message": "Validation failed",
 *   "errors": {...}
 * }
 */
public function updateProfile(UpdateOwnerMeRequest $request)
{
    /** @var \App\Models\User $user */
    $user = $request->user();  // ✅ Use request->user()
    
    $validatedData = $request->validated();

    return DB::transaction(function () use ($user, $validatedData, $request) {
        $userData = array_filter([
            'username' => $validatedData['username'] ?? $user->username,
            'first_name' => $validatedData['first_name'] ?? $user->first_name,
            'last_name' => $validatedData['last_name'] ?? $user->last_name,
        ]);
        $user->update($userData);

        $ownerData = array_filter([
            'bio' => $validatedData['bio'] ?? $user->owner?->bio,
            'country' => $validatedData['country'] ?? $user->owner?->country,
        ]);

        if (!$user->owner()->exists()) {
            $user->owner()->create($ownerData);
        } else {
            $user->owner()->update($ownerData);
        }

        if ($request->hasFile('photo')) {
            $user->uploadMedia($request->file('photo'), 'avatar');
        }


                $user->refresh(); 
        $user->load(['owner', 'photo']);

        return $this->sendResponse(
            new OwnerResource($user),
            'Profile updated successfully.'
        );
    });
}

}
