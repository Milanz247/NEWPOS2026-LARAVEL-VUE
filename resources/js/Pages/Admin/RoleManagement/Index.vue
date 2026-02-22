<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { confirmDelete } from '@/plugins/alert.js';
import { useTranslation } from '@/lib/useTranslation.js';

const { t } = useTranslation();

defineProps({ roles: Object });

const deleteRole = async (id, name) => {
    const ok = await confirmDelete({
        title: `${t('delete')} "${name}"?`,
        text:  t('delete_role_warning'),
    });
    if (ok) {
        useForm({}).delete(route('admin.roles.destroy', id));
    }
};
</script>

<template>
    <Head :title="t('roles_permissions')" />
    <AdminLayout>
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">{{ t('roles_permissions') }}</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">{{ t('manage_roles') }}</p>
            </div>
            <Link :href="route('admin.roles.create')" class="btn-primary shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">{{ t('create_role') }}</span>
                <span class="sm:hidden">{{ t('new') }}</span>
            </Link>
        </div>

        <!-- Desktop Table -->
        <div class="card overflow-hidden hidden sm:block">
            <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ t('role_name') }}</th>
                        <th>{{ t('permissions') }}</th>
                        <th class="text-right">{{ t('actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="role in roles.data" :key="role.id">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold shrink-0" :class="role.name === 'Owner' ? 'bg-brand-dark text-white' : 'bg-brand-lighter text-brand-dark'">
                                    {{ role.name.charAt(0) }}
                                </div>
                                <span class="font-semibold text-brand-dark">{{ role.name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm text-brand-accent">
                                {{ role.name === 'Owner' ? t('all_permissions') : `${role.permissions.length} ${t('permission_count')}` }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="route('admin.roles.show', role.id)" class="btn-secondary text-xs py-1.5 px-3">{{ t('view') }}</Link>
                                <template v-if="role.name !== 'Owner'">
                                    <Link :href="route('admin.roles.edit', role.id)" class="btn-secondary text-xs py-1.5 px-3">{{ t('edit') }}</Link>
                                    <button @click="deleteRole(role.id, role.name)" class="btn-danger text-xs py-1.5 px-3">{{ t('delete') }}</button>
                                </template>
                                <span v-else class="badge badge-dark">{{ t('protected') }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!roles.data || roles.data.length === 0">
                        <td colspan="3" class="text-center py-12 text-brand-muted">{{ t('no_records') }}</td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="roles.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-brand-border">
            <p class="text-xs text-brand-muted">
                {{ t('showing') }} {{ roles.from }}–{{ roles.to }} {{ t('of') }} {{ roles.total }}
            </p>
            <div class="flex gap-1">
                <template v-for="(link, i) in roles.links" :key="i">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-3 py-1.5 text-xs rounded-lg border transition-colors"
                        :class="link.active ? 'bg-brand-dark text-white border-brand-dark' : 'border-brand-border text-brand-accent hover:bg-brand-light'"
                        v-html="link.label"
                        preserve-state
                        preserve-scroll
                    />
                    <span v-else class="px-3 py-1.5 text-xs text-brand-muted" v-html="link.label" />
                </template>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-3">
            <div v-for="role in roles.data" :key="role.id" class="card p-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold shrink-0" :class="role.name === 'Owner' ? 'bg-brand-dark text-white' : 'bg-brand-lighter text-brand-dark'">
                            {{ role.name.charAt(0) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-brand-dark text-sm truncate">{{ role.name }}</p>
                            <p class="text-xs text-brand-muted">
                                {{ role.name === 'Owner' ? t('all_permissions') : `${role.permissions.length} ${t('permission_count')}` }}
                            </p>
                        </div>
                    </div>
                    <span v-if="role.name === 'Owner'" class="badge badge-dark text-xs shrink-0">{{ t('protected') }}</span>
                </div>
                <div class="flex gap-2 mt-3 pt-3 border-t border-brand-border">
                    <Link :href="route('admin.roles.show', role.id)" class="btn-secondary text-xs py-1.5 px-3 flex-1 text-center">{{ t('view') }}</Link>
                    <template v-if="role.name !== 'Owner'">
                        <Link :href="route('admin.roles.edit', role.id)" class="btn-secondary text-xs py-1.5 px-3 flex-1 text-center">{{ t('edit') }}</Link>
                        <button @click="deleteRole(role.id, role.name)" class="btn-danger text-xs py-1.5 px-3 flex-1">{{ t('delete') }}</button>
                    </template>
                </div>
            </div>
            <div v-if="!roles.data || roles.data.length === 0" class="card p-8 text-center text-brand-muted text-sm">
                {{ t('no_records') }}
            </div>

            <!-- Mobile Pagination -->
            <div v-if="roles.last_page > 1" class="flex items-center justify-between">
                <Link
                    v-if="roles.prev_page_url"
                    :href="roles.prev_page_url"
                    class="btn-secondary text-xs py-1.5 px-4"
                    preserve-state preserve-scroll
                >← {{ t('previous') }}</Link>
                <span class="text-xs text-brand-muted">{{ roles.current_page }} / {{ roles.last_page }}</span>
                <Link
                    v-if="roles.next_page_url"
                    :href="roles.next_page_url"
                    class="btn-secondary text-xs py-1.5 px-4"
                    preserve-state preserve-scroll
                >{{ t('next') }} →</Link>
                <span v-else />
            </div>
        </div>
    </AdminLayout>
</template>
