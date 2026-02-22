<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page     = usePage();
const user     = computed(() => page.props.auth?.user);
const business = computed(() => page.props.business);
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">Welcome back, {{ user?.name }}</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-5 mb-4 sm:mb-6">
            <div class="card p-4 sm:p-6 hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-green-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge badge-success">Active</span>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-brand-dark mt-1">Online</p>
                <p class="text-xs text-brand-muted mt-1">System Status</p>
            </div>

            <div class="card p-4 sm:p-6 hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-brand-lighter flex items-center justify-center">
                        <svg class="w-5 h-5 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="badge badge-dark">{{ user?.roles?.[0] ?? 'User' }}</span>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-brand-dark mt-1">{{ user?.roles?.[0] ?? '—' }}</p>
                <p class="text-xs text-brand-muted mt-1">Your Access Level</p>
            </div>

            <div class="card p-4 sm:p-6 hover-lift">
                <div class="flex justify-between items-start mb-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="badge badge-default">Stable</span>
                </div>
                <p class="text-xl sm:text-2xl font-bold text-brand-dark mt-1">{{ $page.props.appVersion }}</p>
                <p class="text-xs text-brand-muted mt-1">System Build</p>
            </div>
        </div>

        <!-- Business Info card -->
        <div v-if="business" class="card p-4 sm:p-6">
            <p class="text-sm font-semibold text-brand-dark mb-4">Business Overview</p>
            <div class="flex items-center gap-3 sm:gap-4">
                <div v-if="business.logo" class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl overflow-hidden border border-brand-border shrink-0">
                    <img :src="business.logo" class="w-full h-full object-contain" alt="Business Logo" />
                </div>
                <div v-else class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-brand-lighter flex items-center justify-center shrink-0">
                    <svg class="w-7 h-7 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-brand-dark text-base sm:text-lg truncate">{{ business.shop_name }}</p>
                    <p class="text-xs sm:text-sm text-brand-muted truncate">Currency: <strong>{{ business.currency }}</strong> · Timezone: <strong>{{ business.timezone }}</strong></p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
