<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useTranslation } from '@/lib/useTranslation.js';

const { t } = useTranslation();

defineProps({ user: Object });
</script>

<template>
    <Head :title="user.name" />
    <AdminLayout>
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">{{ user.name }}</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">{{ t('staff_profile') }}</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <Link v-if="!user.roles.some(r => r.name === 'Owner')" :href="route('admin.users.edit', user.id)" class="btn-primary text-sm">{{ t('edit') }}</Link>
                <Link :href="route('admin.users.index')" class="btn-secondary text-sm">← {{ t('back') }}</Link>
            </div>
        </div>

        <div class="max-w-2xl">
            <div class="card p-4 sm:p-8 space-y-4 sm:space-y-6">
                <!-- Avatar + name -->
                <div class="flex items-center gap-3 sm:gap-4 pb-4 sm:pb-6 border-b border-brand-border">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-brand-dark text-white flex items-center justify-center text-xl sm:text-2xl font-bold shadow-soft-md shrink-0">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-lg sm:text-xl font-bold text-brand-dark truncate">{{ user.name }}</p>
                        <p class="text-xs sm:text-sm text-brand-muted truncate">{{ user.email }}</p>
                    </div>
                </div>

                <!-- Details grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 text-sm">
                    <div>
                        <p class="input-label">{{ t('role') }}</p>
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            <span v-for="role in user.roles" :key="role.id" :class="role.name === 'Owner' ? 'badge badge-dark' : 'badge badge-default'">{{ role.name }}</span>
                            <span v-if="user.roles.length === 0" class="text-brand-muted italic">{{ t('no_records') }}</span>
                        </div>
                    </div>
                    <div>
                        <p class="input-label">{{ t('location') }}</p>
                        <p class="font-medium text-brand-dark mt-1">
                            <span v-if="user.business_location" class="badge badge-default">{{ user.business_location.name }}</span>
                            <span v-else class="text-brand-muted italic">{{ t('no_location') }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="input-label">{{ t('member_since') }}</p>
                        <p class="font-medium text-brand-dark mt-1">{{ new Date(user.created_at).toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' }) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
