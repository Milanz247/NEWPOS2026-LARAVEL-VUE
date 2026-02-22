<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BusinessLocation;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'businessLocation']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Location filter
        if ($request->filled('location_id')) {
            $query->where('business_location_id', $request->location_id);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/UserManagement/Index', [
            'users'     => $users,
            'locations' => BusinessLocation::where('is_active', true)->get(),
            'filters'   => $request->only(['search', 'location_id']),
        ]);
    }

    public function create()
    {
        $roles     = Role::all();
        $locations = BusinessLocation::where('is_active', true)->get();

        return Inertia::render('Admin/UserManagement/Create', [
            'roles'     => $roles,
            'locations' => $locations,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'password'             => 'required|string|min:8|confirmed',
            'role'                 => 'required|string|exists:roles,name',
            'business_location_id' => 'nullable|exists:business_locations,id',
        ]);

        $user = User::create([
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'password'             => Hash::make($validated['password']),
            'business_location_id' => $validated['business_location_id'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'businessLocation']);
        return Inertia::render('Admin/UserManagement/View', [
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        $user->load(['roles', 'businessLocation']);
        $roles     = Role::all();
        $locations = BusinessLocation::where('is_active', true)->get();

        return Inertia::render('Admin/UserManagement/Edit', [
            'user'      => $user,
            'roles'     => $roles,
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'             => 'nullable|string|min:8|confirmed',
            'role'                 => 'required|string|exists:roles,name',
            'business_location_id' => 'nullable|exists:business_locations,id',
        ]);

        $user->name                 = $validated['name'];
        $user->email                = $validated['email'];
        $user->business_location_id = $validated['business_location_id'] ?? null;
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        if ($user->hasRole('Owner') && $validated['role'] !== 'Owner') {
            return redirect()->back()->withErrors('Cannot remove Owner role from this user.');
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('Owner')) {
            return redirect()->back()->withErrors('Cannot delete the Owner.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
