<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ role: Object, groupedPermissions: Object });

const form = useForm({
    name:        props.role.name,
    permissions: props.role.permissions.map(p => p.name),
});

const submit = () => form.put(route('admin.roles.update', props.role.id));
</script>

<template>
    <Head title="Edit Role" />
    <AdminLayout>
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">Edit Role: {{ role.name }}</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">Modify role name and assigned permissions</p>
            </div>
            <Link :href="route('admin.roles.index')" class="btn-secondary text-sm shrink-0">← Back</Link>
        </div>

        <div class="card p-4 sm:p-8">
            <div v-if="role.name === 'Owner'" class="p-4 rounded-xl bg-amber-50 border border-amber-200 flex gap-3 items-start mb-8">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <p class="text-sm text-amber-800 font-medium">The <strong>Owner</strong> role has all permissions by default and cannot be modified.</p>
            </div>

            <form v-else @submit.prevent="submit" class="space-y-8">
                <div v-if="form.hasErrors" class="p-4 rounded-xl bg-red-50 border border-red-200">
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600 font-medium">{{ error }}</li>
                    </ul>
                </div>

                <div class="max-w-sm">
                    <InputLabel for="name" value="Role Name" />
                    <TextInput id="name" type="text" class="mt-1.5 block w-full" v-model="form.name" required />
                    <InputError :message="form.errors.name" />
                </div>

                <div>
                    <p class="text-sm font-semibold text-brand-dark mb-1">Permissions</p>
                    <p class="text-xs text-brand-muted mb-5">Check or uncheck permissions for this role.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(permissions, groupName) in groupedPermissions" :key="groupName" class="card p-5">
                            <h4 class="text-xs font-bold uppercase tracking-widest text-brand-muted mb-3">{{ groupName }}</h4>
                            <div class="space-y-2">
                                <label v-for="perm in permissions" :key="perm.id" class="flex items-center gap-2.5 text-sm text-brand-dark cursor-pointer hover:text-brand-dark group">
                                    <input type="checkbox" class="w-4 h-4 rounded border-brand-border text-brand-dark focus:ring-brand-ring" :value="perm.name" v-model="form.permissions" />
                                    <span class="group-hover:font-medium transition-all">{{ perm.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-brand-border flex justify-end">
                    <button type="submit" class="btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Update Role' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
