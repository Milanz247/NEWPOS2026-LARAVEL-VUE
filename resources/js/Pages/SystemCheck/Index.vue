<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    phpVersion:          String,
    isPhpValid:          Boolean,
    isDbConnected:       Boolean,
    isComposerInstalled: Boolean,
    isPublicReadable:    Boolean,
    allGood:             Boolean,
    appVersion:          String,
});
</script>

<template>
    <Head title="System Requirements" />

    <div class="min-h-screen bg-brand-light flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-2xl">

            <!-- Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-dark mb-5 shadow-soft-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-brand-dark tracking-tight">System Requirements</h1>
                <p class="text-brand-muted text-sm mt-2">Verifying your environment before setup</p>
                <span class="badge badge-default mt-3 inline-flex">{{ appVersion }}</span>
            </div>

            <!-- Checks Card -->
            <div class="card p-2 mb-6 shadow-soft-lg">

                <!-- PHP -->
                <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-brand-light transition-colors">
                    <div :class="isPhpValid ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500'" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-brand-dark text-sm">PHP Version</p>
                        <p class="text-xs text-brand-muted mt-0.5">Required: ≥ 8.2 &nbsp;·&nbsp; Detected: {{ phpVersion }}</p>
                    </div>
                    <span :class="isPhpValid ? 'badge badge-success' : 'badge badge-danger'">{{ isPhpValid ? 'Passed' : 'Failed' }}</span>
                </div>

                <div class="h-px bg-brand-border mx-4" />

                <!-- Database -->
                <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-brand-light transition-colors">
                    <div :class="isDbConnected ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500'" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-brand-dark text-sm">MySQL Database</p>
                        <p class="text-xs text-brand-muted mt-0.5">Testing .env database credentials</p>
                    </div>
                    <span :class="isDbConnected ? 'badge badge-success' : 'badge badge-danger'">{{ isDbConnected ? 'Connected' : 'Failed' }}</span>
                </div>

                <div class="h-px bg-brand-border mx-4" />

                <!-- Composer -->
                <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-brand-light transition-colors">
                    <div :class="isComposerInstalled ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500'" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-brand-dark text-sm">Composer Dependencies</p>
                        <p class="text-xs text-brand-muted mt-0.5">Checking vendor directory</p>
                    </div>
                    <span :class="isComposerInstalled ? 'badge badge-success' : 'badge badge-danger'">{{ isComposerInstalled ? 'Installed' : 'Missing' }}</span>
                </div>

                <div class="h-px bg-brand-border mx-4" />

                <!-- Public -->
                <div class="flex items-center gap-4 p-4 rounded-xl hover:bg-brand-light transition-colors">
                    <div :class="isPublicReadable ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500'" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-brand-dark text-sm">Public Directory</p>
                        <p class="text-xs text-brand-muted mt-0.5">Checking public/index.php accessibility</p>
                    </div>
                    <span :class="isPublicReadable ? 'badge badge-success' : 'badge badge-danger'">{{ isPublicReadable ? 'Accessible' : 'Locked' }}</span>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center">
                <div v-if="allGood">
                    <p class="text-sm text-brand-muted mb-5">All requirements met. You're ready to proceed.</p>
                    <Link :href="route('registration.index')" class="btn-primary inline-flex mx-auto px-8 py-3">
                        Continue to Setup
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </Link>
                </div>
                <div v-else class="p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-700 font-medium">
                    Please fix the failed requirements above before continuing.
                </div>
            </div>
        </div>
    </div>
</template>
