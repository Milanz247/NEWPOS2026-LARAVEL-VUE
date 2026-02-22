<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    private function getGroupedPermissions()
    {
        return Permission::all()->groupBy(function($perm) {
            $name = $perm->name;
            if (str_contains($name, 'inventory')) return 'Inventory Permissions';
            if (str_contains($name, 'sales')) return 'Sales Permissions';
            if (str_contains($name, 'phones')) return 'Buyback Permissions';
            if (str_contains($name, 'reports')) return 'Reports Permissions';
            if (str_contains($name, 'users') || str_contains($name, 'roles') || str_contains($name, 'business settings')) return 'Management Permissions';
            return 'Other Permissions';
        });
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        return Inertia::render('Admin/RoleManagement/Index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/RoleManagement/Create', [
            'groupedPermissions' => $this->getGroupedPermissions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        return Inertia::render('Admin/RoleManagement/View', [
            'role' => $role,
        ]);
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        return Inertia::render('Admin/RoleManagement/Edit', [
            'role' => $role,
            'groupedPermissions' => $this->getGroupedPermissions(),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Owner') {
            return redirect()->back()->withErrors('The Owner role cannot be modified.');
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $validated['name']]);
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Owner') {
            return redirect()->back()->withErrors('The Owner role cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
