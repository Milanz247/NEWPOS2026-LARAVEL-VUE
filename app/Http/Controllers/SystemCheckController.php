<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemCheckController extends Controller
{
    public function index()
    {
        $setup = Business::first();
        if ($setup && $setup->is_setup_completed) {
            return redirect('/dashboard');
        }

        // Requirements Checking Logic
        $phpVersion = PHP_VERSION;
        $isPhpValid = version_compare($phpVersion, '8.2.0', '>=');

        // Check DB Connection
        $isDbConnected = false;
        try {
            DB::connection()->getPdo();
            $isDbConnected = true;
        } catch (\Exception $e) {
            $isDbConnected = false;
        }

        // Check if Composer vendor directory exists
        $isComposerInstalled = is_dir(base_path('vendor'));

        // Check public folder mapped successfully (assuming index.php exists in public)
        $isPublicReadable = is_readable(public_path('index.php'));

        $allGood = $isPhpValid && $isDbConnected && $isComposerInstalled && $isPublicReadable;

        return Inertia::render('SystemCheck/Index', [
            'phpVersion' => $phpVersion,
            'isPhpValid' => $isPhpValid,
            'isDbConnected' => $isDbConnected,
            'isComposerInstalled' => $isComposerInstalled,
            'isPublicReadable' => $isPublicReadable,
            'allGood' => $allGood,
            'appVersion' => 'v1.0.0',
        ]);
    }
}
