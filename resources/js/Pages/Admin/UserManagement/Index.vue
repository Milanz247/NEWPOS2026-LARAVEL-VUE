<script setup>
import { ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { confirmDelete } from '@/plugins/alert.js';
import { useTranslation } from '@/lib/useTranslation.js';

const { t } = useTranslation();

const props = defineProps({
    users:     Object,   // paginator object { data, links, current_page, last_page, total, from, to }
    locations: Array,
    filters:   Object,
});

const search     = ref(props.filters?.search ?? '');
const locationId = ref(props.filters?.location_id ?? '');

let debounceTimer = null;
const applyFilters = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('admin.users.index'), {
            search:      search.value || undefined,
            location_id: locationId.value || undefined,
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 300);
};

watch(search,     applyFilters);
watch(locationId, applyFilters);

const clearFilters = () => {
    search.value     = '';
    locationId.value = '';
};

const deleteUser = async (id, name) => {
    const ok = await confirmDelete({
        title: `${t('delete')} "${name}"?`,
        text:  t('delete_user_warning'),
    });
    if (ok) useForm({}).delete(route('admin.users.destroy', id));
};
</script>

<template>
    <Head :title="t('staff_management')" />

    <AdminLayout>
        <!-- Page Header -->
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">{{ t('staff_management') }}</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">{{ t('manage_staff') }}</p>
            </div>
            <Link :href="route('admin.users.create')" class="btn-primary shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden sm:inline">{{ t('add_staff') }}</span>
                <span class="sm:hidden">{{ t('new') }}</span>
            </Link>
        </div>

        <!-- Filters Bar -->
        <div class="card p-3 sm:p-4 mb-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-brand-muted pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input
                        v-model="search"
                        type="text"
                        :placeholder="t('search_users')"
                        class="input-field pl-10 w-full"
                    />
                </div>
                <!-- Location Filter -->
                <select v-model="locationId" class="input-field sm:w-48">
                    <option value="">{{ t('all_locations') }}</option>
                    <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                </select>
                <!-- Clear Filters -->
                <button v-if="search || locationId" @click="clearFilters" class="btn-secondary text-xs shrink-0">
                    {{ t('clear') }}
                </button>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="card overflow-hidden hidden sm:block">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ t('name') }}</th>
                            <th>{{ t('email') }}</th>
                            <th>{{ t('role') }}</th>
                            <th>{{ t('location') }}</th>
                            <th class="text-right">{{ t('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-brand-dark text-white flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="font-semibold text-brand-dark">{{ user.name }}</span>
                                </div>
                            </td>
                            <td class="text-brand-accent">{{ user.email }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1.5">
                                    <span v-for="role in user.roles" :key="role.id" :class="role.name === 'Owner' ? 'badge badge-dark' : 'badge badge-default'">{{ role.name }}</span>
                                </div>
                            </td>
                            <td>
                                <span v-if="user.business_location" class="badge badge-default">{{ user.business_location.name }}</span>
                                <span v-else class="text-brand-muted text-xs">—</span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="route('admin.users.show', user.id)" class="btn-secondary text-xs py-1.5 px-3">{{ t('view') }}</Link>
                                    <Link v-if="!user.roles.some(r => r.name === 'Owner')" :href="route('admin.users.edit', user.id)" class="btn-secondary text-xs py-1.5 px-3">{{ t('edit') }}</Link>
                                    <button v-if="!user.roles.some(r => r.name === 'Owner')" @click="deleteUser(user.id, user.name)" class="btn-danger text-xs py-1.5 px-3">{{ t('delete') }}</button>
                                    <span v-if="user.roles.some(r => r.name === 'Owner')" class="badge badge-dark">{{ t('protected') }}</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.data || users.data.length === 0">
                            <td colspan="5" class="text-center py-12 text-brand-muted">{{ t('no_records') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-brand-border">
                <p class="text-xs text-brand-muted">
                    {{ t('showing') }} {{ users.from }}–{{ users.to }} {{ t('of') }} {{ users.total }}
                </p>
                <div class="flex gap-1">
                    <template v-for="(link, i) in users.links" :key="i">
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
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-3">
            <div v-for="user in users.data" :key="user.id" class="card p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-brand-dark text-white flex items-center justify-center text-sm font-bold shrink-0">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-brand-dark text-sm truncate">{{ user.name }}</p>
                            <p class="text-xs text-brand-muted truncate">{{ user.email }}</p>
                        </div>
                    </div>
                    <span v-if="user.roles.some(r => r.name === 'Owner')" class="badge badge-dark text-xs shrink-0">{{ t('protected') }}</span>
                </div>

                <div class="flex flex-wrap gap-1.5 mt-3">
                    <span v-for="role in user.roles" :key="role.id" :class="role.name === 'Owner' ? 'badge badge-dark' : 'badge badge-default'" class="text-xs">{{ role.name }}</span>
                    <span v-if="user.business_location" class="badge badge-default text-xs">{{ user.business_location.name }}</span>
                </div>

                <div v-if="!user.roles.some(r => r.name === 'Owner')" class="flex gap-2 mt-3 pt-3 border-t border-brand-border">
                    <Link :href="route('admin.users.show', user.id)" class="btn-secondary text-xs py-1.5 px-3 flex-1 text-center">{{ t('view') }}</Link>
                    <Link :href="route('admin.users.edit', user.id)" class="btn-secondary text-xs py-1.5 px-3 flex-1 text-center">{{ t('edit') }}</Link>
                    <button @click="deleteUser(user.id, user.name)" class="btn-danger text-xs py-1.5 px-3 flex-1">{{ t('delete') }}</button>
                </div>
                <div v-else class="mt-3 pt-3 border-t border-brand-border">
                    <Link :href="route('admin.users.show', user.id)" class="btn-secondary text-xs py-1.5 px-3 w-full text-center block">{{ t('view') }}</Link>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="!users.data || users.data.length === 0" class="card p-8 text-center text-brand-muted text-sm">
                {{ t('no_records') }}
            </div>

            <!-- Mobile Pagination -->
            <div v-if="users.last_page > 1" class="flex items-center justify-between">
                <Link
                    v-if="users.prev_page_url"
                    :href="users.prev_page_url"
                    class="btn-secondary text-xs py-1.5 px-4"
                    preserve-state preserve-scroll
                >← {{ t('previous') }}</Link>
                <span v-else />
                <span class="text-xs text-brand-muted">{{ users.current_page }} / {{ users.last_page }}</span>
                <Link
                    v-if="users.next_page_url"
                    :href="users.next_page_url"
                    class="btn-secondary text-xs py-1.5 px-4"
                    preserve-state preserve-scroll
                >{{ t('next') }} →</Link>
                <span v-else />
            </div>
        </div>
    </AdminLayout>
</template>
