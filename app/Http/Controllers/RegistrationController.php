<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function index()
    {
        $setup = Business::first();
        if ($setup && $setup->is_setup_completed) {
            return redirect('/dashboard');
        }

        return Inertia::render('Registration/Index');
    }

    public function store(Request $request)
    {
        $setup = Business::first();
        if ($setup && $setup->is_setup_completed) {
            return redirect('/dashboard');
        }

        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'currency' => 'required|string',
            
            // Owner credentials
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8|confirmed',
        ]);

        // Run the permissions seeder for the first time
        Artisan::call('db:seed', ['--class' => 'PermissionSeeder']);

        // Create Business
        $business = Business::create([
            'name'               => $validated['shop_name'],
            'currency'           => $validated['currency'],
            'start_date'         => now()->toDateString(),
            'is_setup_completed' => true,
        ]);

        // Auto-create first "Main Location"
        $location = BusinessLocation::create([
            'business_id' => $business->id,
            'location_id' => BusinessLocation::generateNextLocationId(),
            'name'        => $validated['shop_name'],
            'city'        => $validated['address'],
            'mobile'      => $validated['phone'] ? [$validated['phone']] : null,
            'email'       => $validated['email'],
            'is_active'   => true,
        ]);

        // Create Owner and assign to the main location
        $user = User::create([
            'name'                 => $validated['owner_name'],
            'email'                => $validated['owner_email'],
            'password'             => Hash::make($validated['owner_password']),
            'business_location_id' => $location->id,
        ]);
        
        // Assign Role
        $user->assignRole('Owner');

        // Log the user in
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration completed successfully.');
    }
}
