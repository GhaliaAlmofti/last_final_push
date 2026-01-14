<?php

namespace App\Http\Controllers\Admin\Users\Author;

use App\Models\User;
use App\Enums\User\UserType;
use App\Enums\User\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\Author\StoreAuthorRequest;
use App\Http\Resources\Admin\Users\Author\AuthorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; 

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $authors = User::query()
            ->where('type', UserType::Author)
            ->with(['author', 'photo'])
            ->when($request->search, function ($query, $search) {
                $query->searchByName($search);
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->integer('per_page', 15)); 

        $resource = AuthorResource::collection($authors);

        return $this->sendResponse(
            $resource->response()->getData(true), 
            'Authors retrieved successfully.'
        );
    }

    public function show(User $user)
    {
        if ($user->type !== UserType::Author) {
            return response()->json(['message' => 'User is not an author.'], 422);
        }

        return $this->sendResponse(
            AuthorResource::make($user->load(['author', 'photo'])),
            'Author retrieved successfully.'
        );
    }

    /**
     * Admin creating an Author directly
     */
    public function store(StoreAuthorRequest $request)
    {
        $validatedData = $request->validated();
        
        try {
                DB::beginTransaction();
                $user = User::create([
                    'username' => $validatedData['username'],
                    'first_name'     => $validatedData['first_name'],
                    'last_name'     => $validatedData['last_name'],
                    'password' => $validatedData['password'],
                    'type'     => UserType::Organizer,
                ]);

                $user->owner()->create([
                    'bio' => $validatedData['bio'] ?? null,
                    'country' => $validatedData['country'] ?? null,
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
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        return $this->sendResponse(
                 AuthorResource::make($user->load(['author', 'photo'])),
                'Author created successfully.',
                201
            );
    }

    public function approve(User $user)
    {
        if ($user->type !== UserType::Author || $user->status !== UserStatus::Pending) {
            return response()->json(['message' => 'User is not a pending author.'], 422);
        }
        
        $user->update(['status' => UserStatus::Active]);
        return $this->sendResponse(null, "Author '{$user->username}' has been approved.");
    }

    public function block(User $user)
    {
        if ($user->status === UserStatus::Blocked) {
            return response()->json(['message' => 'User is already blocked.'], 422);
        }

        $user->update(['status' => UserStatus::Blocked]);
        return $this->sendResponse(null, "Author '{$user->username}' has been blocked.");
    }

    public function update(Request $request, User $user)
    {
        if ($user->type !== UserType::Author) {
            return response()->json(['message' => 'User is not an author.'], 422);
        }

        $validatedData = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user->update([
                'first_name' => $validatedData['first_name'] ?? $user->first_name,
                'last_name' => $validatedData['last_name'] ?? $user->last_name,
            ]);

            $user->author()->update([
                'bio' => $validatedData['bio'] ?? $user->author->bio,
                'country' => $validatedData['country'] ?? $user->author->country,
            ]);

            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo->file_path);
                    $user->photo()->delete();
                }

                $file = $request->file('photo');
                $path = $file->store('authors/avatars', 'public');

                $user->photo()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'collection' => 'avatar',
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return $this->sendResponse(
            AuthorResource::make($user->load(['author', 'photo'])),
            'Author updated successfully.'
        );
    }

    public function destroy(User $user)
    {
        if ($user->type !== UserType::Author) {
            return $this->sendError('Invalid Asset Type', ['error' => 'User is not an author.'], 422);
        }

        if ($user->books()->exists()) {
            return $this->sendError(
                'Conflict', 
                ['error' => 'Cannot delete author with active book records. Archive books first.'], 
                409
            );
        }

        try {
            return DB::transaction(function () use ($user) {
                if ($user->photo && Storage::disk('public')->exists($user->photo->file_path)) {
                    Storage::disk('public')->delete($user->photo->file_path);
                }
                $user->delete();

                return $this->sendResponse(null, 'Author asset decommissioned successfully.');
            });
        } catch (\Throwable $e) {
            Log::error("Author Deletion Failed", [
                'author_id' => $user->id,
                'executor_id' => auth()->id(),
                'exception' => $e->getMessage()
            ]);

            return $this->sendError('Critical failure during deletion.', [], 500);
        }
    }
}
