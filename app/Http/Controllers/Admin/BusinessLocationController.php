<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessLocation;
use Illuminate\Http\Request;

class BusinessLocationController extends Controller
{
    public function index(Request $request)
    {
        $business = Business::first();

        $query = BusinessLocation::where('business_id', $business->id)
            ->withCount('users');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location_id', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $locations = $query->latest()->paginate(10)->withQueryString();

        return response()->json($locations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'landmark'         => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'zip_code'         => 'nullable|string|max:20',
            'state'            => 'nullable|string|max:255',
            'country'          => 'nullable|string|max:255',
            'mobile'           => 'nullable|array',
            'mobile.*'         => 'nullable|string|max:50',
            'alternate_number' => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'website'          => 'nullable|string|max:255',
            'is_active'        => 'boolean',
        ]);

        $business = Business::first();

        $validated['business_id'] = $business->id;
        $validated['location_id'] = BusinessLocation::generateNextLocationId();

        // Filter out empty mobile numbers
        if (isset($validated['mobile'])) {
            $validated['mobile'] = array_values(array_filter($validated['mobile'], fn($m) => !empty(trim($m))));
            if (empty($validated['mobile'])) {
                $validated['mobile'] = null;
            }
        }

        BusinessLocation::create($validated);

        return redirect()->back()->with('success', 'Business location created successfully.');
    }

    public function update(Request $request, BusinessLocation $location)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'landmark'         => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'zip_code'         => 'nullable|string|max:20',
            'state'            => 'nullable|string|max:255',
            'country'          => 'nullable|string|max:255',
            'mobile'           => 'nullable|array',
            'mobile.*'         => 'nullable|string|max:50',
            'alternate_number' => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'website'          => 'nullable|string|max:255',
            'is_active'        => 'boolean',
        ]);

        // Filter out empty mobile numbers
        if (isset($validated['mobile'])) {
            $validated['mobile'] = array_values(array_filter($validated['mobile'], fn($m) => !empty(trim($m))));
            if (empty($validated['mobile'])) {
                $validated['mobile'] = null;
            }
        }

        $location->update($validated);

        return redirect()->back()->with('success', 'Business location updated successfully.');
    }

    public function destroy(BusinessLocation $location)
    {
        if ($location->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete location with assigned users. Reassign users first.');
        }

        $location->delete();
        return redirect()->back()->with('success', 'Business location deleted successfully.');
    }
}
