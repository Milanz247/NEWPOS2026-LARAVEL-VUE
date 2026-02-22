<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({ role: Object });
</script>

<template>
    <Head title="View Role" />
    <AdminLayout>
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">{{ role.name }}</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">Role details and permissions overview</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <Link v-if="role.name !== 'Owner'" :href="route('admin.roles.edit', role.id)" class="btn-primary text-sm">Edit Role</Link>
                <Link :href="route('admin.roles.index')" class="btn-secondary text-sm">← Back</Link>
            </div>
        </div>

        <div class="max-w-3xl space-y-4 sm:space-y-5">
            <!-- Role info card -->
            <div class="card p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl font-bold shadow-soft-sm shrink-0" :class="role.name === 'Owner' ? 'bg-brand-dark text-white' : 'bg-brand-lighter text-brand-dark'">
                    {{ role.name.charAt(0) }}
                </div>
                <div>
                    <p class="text-xl font-bold text-brand-dark">{{ role.name }}</p>
                    <p class="text-sm text-brand-muted mt-0.5">
                        {{ role.name === 'Owner' ? 'Superadmin — All permissions granted by default' : `${role.permissions.length} permission(s) assigned` }}
                    </p>
                </div>
                <div class="sm:ml-auto mt-2 sm:mt-0">
                    <span :class="role.name === 'Owner' ? 'badge badge-dark' : 'badge badge-default'">
                        {{ role.name === 'Owner' ? 'Protected' : 'Custom' }}
                    </span>
                </div>
            </div>

            <!-- Permissions card -->
            <div class="card p-4 sm:p-6">
                <p class="text-sm font-semibold text-brand-dark mb-4">Assigned Permissions</p>

                <div v-if="role.name === 'Owner'" class="p-4 rounded-xl bg-brand-lighter text-brand-accent text-sm font-medium">
                    The Owner role inherits all system permissions automatically.
                </div>

                <div v-else class="flex flex-wrap gap-2">
                    <span v-for="permission in role.permissions" :key="permission.id" class="badge badge-default">
                        {{ permission.name }}
                    </span>
                    <span v-if="role.permissions.length === 0" class="text-sm text-brand-muted italic">No permissions assigned yet.</span>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
