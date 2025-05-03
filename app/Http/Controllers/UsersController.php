<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Mail\WelcomeWithPassword;
use App\Models\Role;
use App\Models\User;
use App\Services\ImageUploadService;
use App\Services\PasswordGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UsersController
{
    public function __construct(
        private readonly PasswordGeneratorService $passwordGenerator
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::query();

        if ($request->has('onlyUsers')) {
            $users->whereHas('role', function ($q) {
                $q->where('name', '!=', Role::CLIENT);
            });

            if($request->has('role') && $request->role !== 'all') {
                $users->whereHas('role', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }
        } elseif ($request->has('onlyCustomers')) {
            $users->whereHas('role', function ($q) {
                $q->where('name', Role::CLIENT);
            });

            $users->with('store');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');

            $users->where(function ($query) use ($search) {
                $query->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return PaginationHelper::paginateIfAsked($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();

        if(Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 405);
        }

        // Generate a secure password
        $password = $this->passwordGenerator->generate();
        $data['password'] = bcrypt($password);

        try {
            DB::beginTransaction();

            if (isset($data['image']) && !is_null($data['image'])) {
                $data['image_path'] = (new ImageUploadService())->upload($data['image'], 'users', 'user');
            }

            // Create the user
            $user = User::create($data);

            // Send welcome email with password
            Mail::to($user->email)->send(new WelcomeWithPassword(
                user: $user,
                password: $password,
                userLocale: $data['locale'] ?? config('app.locale')
            ));

            DB::commit();

            return response()->json(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded image if it exists
            if (isset($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }


            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (Auth::user()->role !== Role::ADMIN && Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $request->validated();

        if(Auth::user()->role !== 'admin' && Auth::id() !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->update($request->all());

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if(Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 405);
        }

        $user->delete();

        return response()->noContent();
    }

    public function updateUserImage(User $user)
    {
        if(Auth::user()->role !== 'admin' && Auth::id() !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = request()->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $validated['image'];

        if (empty($image)) {
            return $user;
        }

        try {
            $newPath = (new ImageUploadService())->upload($image, 'users', 'user');

            DB::transaction(function() use ($user, $newPath) {
                $oldPath = $user->image_path;
                $user->update(['image_path' => $newPath]);

                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            });

            return response()->json($user->refresh(), 200);
        }catch (\Exception $e) {
            return response()->json(['message' => 'Error uploading image'], 500);
        }
    }
}
