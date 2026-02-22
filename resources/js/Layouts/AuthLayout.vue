<template>
    <div class="min-h-screen bg-brand-light flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Brand header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-brand-dark mb-4 shadow-soft-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-brand-dark tracking-tight">POS System</h1>
                <p class="text-sm text-brand-muted mt-1">Secure Administrator Access</p>
            </div>

            <!-- Card -->
            <div class="card p-8 shadow-soft-lg">
                <slot />
            </div>
        </div>
    </div>
</template>
