<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({ groupedPermissions: Object });

const form = useForm({ name: '', permissions: [] });
const submit = () => form.post(route('admin.roles.store'));
</script>

<template>
    <Head title="Create Role" />
    <AdminLayout>
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">Create New Role</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">Define a role and assign permissions</p>
            </div>
            <Link :href="route('admin.roles.index')" class="btn-secondary text-sm shrink-0">← Back</Link>
        </div>

        <div class="card p-4 sm:p-8">
            <form @submit.prevent="submit" class="space-y-8">
                <div v-if="form.hasErrors" class="p-4 rounded-xl bg-red-50 border border-red-200">
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="(error, key) in form.errors" :key="key" class="text-sm text-red-600 font-medium">{{ error }}</li>
                    </ul>
                </div>

                <div class="max-w-sm">
                    <InputLabel for="name" value="Role Name" />
                    <TextInput id="name" type="text" class="mt-1.5 block w-full" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
                </div>

                <div>
                    <p class="text-sm font-semibold text-brand-dark mb-1">Permissions</p>
                    <p class="text-xs text-brand-muted mb-5">Select the permissions to grant to this role.</p>
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
                        {{ form.processing ? 'Creating...' : 'Create Role' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
