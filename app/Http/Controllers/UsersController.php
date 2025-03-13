<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UsersController
{
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

        $password = Str::random(12);
        $data['password'] = bcrypt($password);

        $user = User::create($data);


        return response()->json(['user' => $user, 'password' => $password], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if (Auth::user()->role !== Role::ADMIN && Auth::user()->id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 405);
        }
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $request->validated();

        if(Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 405);
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

        return response()->json(null, 204);
    }
}
