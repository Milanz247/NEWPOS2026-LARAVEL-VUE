<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessLocation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class BusinessSettingController extends Controller
{
    public function index(Request $request)
    {
        $settings  = Business::first();
        $locations = BusinessLocation::where('business_id', $settings->id)
            ->withCount('users')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/BusinessSettings/Index', [
            'settings'  => $settings,
            'locations' => $locations,
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'currency'   => 'required|string|max:10',
            'timezone'   => 'required|string|timezone',
        ]);

        Business::updateOrCreate(['id' => 1], $validated);

        return redirect()->back()->with('success', 'Business settings updated successfully.');
    }

    public function updateFinancials(Request $request)
    {
        $validated = $request->validate([
            'currency_symbol'           => 'required|string|max:10',
            'currency_symbol_placement' => 'required|in:before,after',
            'tax_number'                => 'nullable|string|max:50',
            'tax_percentage'            => 'required|numeric|min:0|max:100',
            'default_profit_percent'    => 'required|numeric|min:0|max:100',
        ]);

        Business::updateOrCreate(['id' => 1], $validated);

        return redirect()->back()->with('success', 'Financial settings updated successfully.');
    }

    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'start_date'              => 'nullable|date',
            'fy_start_month'          => 'required|integer|between:1,12',
            'stock_accounting_method' => 'required|in:FIFO,LIFO',
            'transaction_edit_days'   => 'required|integer|min:0|max:365',
            'date_format'             => 'required|string|max:20',
            'time_format'             => 'required|in:12,24',
            'currency_precision'      => 'required|integer|between:0,4',
            'quantity_precision'      => 'required|integer|between:0,4',
        ]);

        Business::updateOrCreate(['id' => 1], $validated);

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $settings = Business::firstOrCreate(['id' => 1]);

        // Delete old logo
        if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
            Storage::disk('public')->delete($settings->logo);
        }

        $path = $request->file('logo')->store('logos', 'public');
        $settings->update(['logo' => $path]);

        return redirect()->back()->with('success', 'Business logo updated successfully.');
    }

    public function deleteLogo()
    {
        $settings = Business::first();
        if ($settings && $settings->logo) {
            if (Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            $settings->update(['logo' => null]);
        }

        return redirect()->back()->with('success', 'Logo removed.');
    }
}
