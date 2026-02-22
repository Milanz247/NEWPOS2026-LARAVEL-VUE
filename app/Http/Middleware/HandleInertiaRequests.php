<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Business;
use Illuminate\Support\Facades\Schema;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $business = null;
        if (Schema::hasTable('businesses')) {
            $b = Business::first();
            if ($b) {
                $business = [
                    'name'                     => $b->name,
                    'shop_name'                => $b->name,
                    'logo'                     => $b->logo ? asset('storage/' . $b->logo) : null,
                    'currency'                 => $b->currency,
                    'currency_symbol'          => $b->currency_symbol ?? '$',
                    'currency_symbol_placement'=> $b->currency_symbol_placement ?? 'before',
                    'timezone'                 => $b->timezone ?? 'UTC',
                    'toast_position'           => $b->toast_position ?? 'top-right',
                ];
            }
        }

        // Load locale and translations
        $locale = app()->getLocale();
        $translations = [];
        $langFile = lang_path("{$locale}.json");
        if (file_exists($langFile)) {
            $translations = json_decode(file_get_contents($langFile), true) ?? [];
        }

        // Get user's location info
        $userLocation = null;
        if ($request->user() && $request->user()->business_location_id) {
            $userLocation = $request->user()->businessLocation?->only(['id', 'name', 'location_id']);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id'                   => $request->user()->id,
                    'name'                 => $request->user()->name,
                    'email'                => $request->user()->email,
                    'business_location_id' => $request->user()->business_location_id,
                    'location'             => $userLocation,
                    'roles'                => $request->user()->getRoleNames(),
                    'permissions'          => $request->user()->getAllPermissions()->pluck('name'),
                ] : null,
            ],
            'business' => $business,
            'locale'       => $locale,
            'translations' => $translations,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info'    => $request->session()->get('info'),
            ],
            'appVersion' => 'v1.0.0',
        ];
    }
}
