<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({ status: String });

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), { onFinish: () => form.reset('password') });
};
</script>

<template>
    <AuthLayout>
        <Head title="Sign In" />

        <div v-if="status" class="mb-6 p-3 rounded-xl bg-green-50 border border-green-200 text-sm text-green-700 font-medium">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email Address" />
                <TextInput id="email" type="email" class="mt-1.5 block w-full" v-model="form.email" required autofocus autocomplete="username" />
                <InputError :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput id="password" type="password" class="mt-1.5 block w-full" v-model="form.password" required autocomplete="current-password" />
                <InputError :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="w-4 h-4 rounded border-brand-border text-brand-dark focus:ring-brand-ring"
                    />
                    <span class="text-sm text-brand-accent group-hover:text-brand-dark transition-colors">Remember me</span>
                </label>
            </div>

            <button
                type="submit"
                class="btn-primary w-full justify-center py-3 text-sm mt-2"
                :class="{ 'opacity-60 cursor-not-allowed': form.processing }"
                :disabled="form.processing"
            >
                <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ form.processing ? 'Signing in...' : 'Sign In' }}
            </button>
        </form>
    </AuthLayout>
</template>
