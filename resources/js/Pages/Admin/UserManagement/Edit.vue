<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useTranslation } from '@/lib/useTranslation.js';

const { t } = useTranslation();

const props = defineProps({ user: Object, roles: Array, locations: Array });

const form = useForm({
    name:                  props.user.name,
    email:                 props.user.email,
    password:              '',
    password_confirmation: '',
    role:                  props.user.roles.length > 0 ? props.user.roles[0].name : '',
    business_location_id:  props.user.business_location_id ?? '',
});

const submit = () => {
    form.put(route('admin.users.update', props.user.id), { onFinish: () => form.reset('password', 'password_confirmation') });
};
</script>

<template>
    <Head :title="t('edit_staff')" />
    <AdminLayout>
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">{{ t('edit_staff') }}: {{ user.name }}</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">{{ t('edit_staff') }}</p>
            </div>
            <Link :href="route('admin.users.index')" class="btn-secondary text-sm shrink-0">← {{ t('back') }}</Link>
        </div>

        <div class="max-w-2xl">
            <div class="card p-4 sm:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <InputLabel for="name" :value="t('name')" />
                        <TextInput id="name" type="text" class="mt-1.5 block w-full" v-model="form.name" required />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="email" :value="t('email')" />
                        <TextInput id="email" type="email" class="mt-1.5 block w-full" v-model="form.email" required />
                        <InputError :message="form.errors.email" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="role" :value="t('role')" />
                            <select id="role" v-model="form.role" required class="input-field mt-1.5" :disabled="user.roles.some(r => r.name === 'Owner')">
                                <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                            </select>
                            <p v-if="user.roles.some(r => r.name === 'Owner')" class="mt-1.5 text-xs text-brand-muted">{{ t('owner_role_protected') }}</p>
                            <InputError :message="form.errors.role" />
                        </div>
                        <div>
                            <InputLabel for="business_location_id" :value="t('location')" />
                            <select id="business_location_id" v-model="form.business_location_id" class="input-field mt-1.5">
                                <option value="">{{ t('no_location') }}</option>
                                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                            </select>
                            <InputError :message="form.errors.business_location_id" />
                        </div>
                    </div>
                    <div class="border-t border-brand-border pt-5">
                        <p class="text-sm font-semibold text-brand-dark mb-1">{{ t('change_password') }}</p>
                        <p class="text-xs text-brand-muted mb-4">{{ t('keep_password') }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="password" :value="t('new_password')" />
                                <TextInput id="password" type="password" class="mt-1.5 block w-full" v-model="form.password" />
                                <InputError :message="form.errors.password" />
                            </div>
                            <div>
                                <InputLabel for="password_confirmation" :value="t('confirm_password')" />
                                <TextInput id="password_confirmation" type="password" class="mt-1.5 block w-full" v-model="form.password_confirmation" />
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-brand-border flex justify-end">
                        <button type="submit" class="btn-primary" :disabled="form.processing">
                            {{ form.processing ? t('saving') : t('update_staff') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
