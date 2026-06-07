<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * GET /api/admin/users
     * List all users with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('roles')->latest();

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%$term%")->orWhere('email', 'like', "%$term%"));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json(UserResource::collection($query->paginate(15)));
    }

    /**
     * POST /api/admin/users
     * Create a new user and assign a role.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', Rule::in(['admin', 'project-manager', 'employee'])],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'phone'     => $data['phone'] ?? null,
            'is_active' => true,
        ]);

        $user->assignRole($data['role']);

        return response()->json(new UserResource($user), 201);
    }

    /**
     * GET /api/admin/users/{user}
     */
    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user->load('roles')));
    }

    /**
     * PUT /api/admin/users/{user}
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'email'     => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password'  => ['nullable', 'string', 'min:8'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'is_active' => ['sometimes', 'boolean'],
            'role'      => ['sometimes', Rule::in(['admin', 'project-manager', 'employee'])],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
            unset($data['role']);
        }

        $user->update($data);

        return response()->json(new UserResource($user->refresh()->load('roles')));
    }

    /**
     * DELETE /api/admin/users/{user}
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully.']);
    }
}
