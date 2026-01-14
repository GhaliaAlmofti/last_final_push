<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Enums\User\UserStatus;
use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendee\Auth\RegisterAttendeeRequest as AuthRegisterAttendeeRequest;
use App\Http\Requests\Customer\Auth\RegisterCustomerRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Register attendee (Customer)
     *
     * Public endpoint to create a new attendee account.
     *
     * @group Customer - Auth
     * @unauthenticated
     * @bodyParam username string required
     * @bodyParam name string required
     * @bodyParam password string required
     * @bodyParam email string required
     * @response 201 {
     *  "message": "Account created successfully",
     *  "user": {"id":1, "username":"alice"}
     * }
     */
    public function register(AuthRegisterAttendeeRequest $request)
    {
        $validatedData = $request->validated();

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $user = User::create([
                'username' => $validatedData['username'],
                'name'     => $validatedData['name'],
                'password' => $validatedData['password'],
                'type'     => UserType::Attendee,
            ]);

            $user->attendee()->create([
                'email'        => $validatedData['email'],
                'phone_number' => $validatedData['phone_number'] ?? null,
            ]);

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $path = $file->store('authors/avatars', 'public');

                $user->photo()->create([
                    'file_path'  => $path,
                    'file_name'  => $request->file('photo')->getClientOriginalName(),
                    'mime_type'  => $request->file('photo')->getMimeType(),
                    'size'       => $request->file('photo')->getSize(),
                    'collection' => 'avatar',
                ]);
            }
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\DB::rollBack();
            throw $th;
        }
    }
}
