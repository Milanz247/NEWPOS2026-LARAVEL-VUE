<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Schema;
use App\Models\Business;

class CheckSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For CLI or if the table doesn't exist yet (before migrations)
        if (app()->runningInConsole() || !Schema::hasTable('businesses')) {
            return $next($request);
        }

        $setup = Business::first();

        $isSetupCompleted = $setup ? $setup->is_setup_completed : false;

        // Skip setup routes and asset routes mapping
        if (!$isSetupCompleted) {
            if (!$request->is('check') && 
                !$request->is('registration') && 
                !$request->is('registration/*') && 
                !$request->is('_debugbar/*') && 
                !$request->is('build/*') && 
                !$request->is('build') && 
                !$request->is('assets/*')) {
                return redirect()->route('check.index');
            }
        }

        return $next($request);
    }
}
