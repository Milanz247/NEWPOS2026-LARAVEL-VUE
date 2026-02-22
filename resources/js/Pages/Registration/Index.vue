<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const form = useForm({
    shop_name:                    "",
    address:                      "",
    phone:                        "",
    email:                        "",
    currency:                     "USD",
    owner_name:                   "",
    owner_email:                  "",
    owner_password:               "",
    owner_password_confirmation:  "",
});

const submit = () => {
    form.post(route("registration.store"), {
        onFinish: () => form.reset("owner_password", "owner_password_confirmation"),
    });
};
</script>

<template>
    <Head title="Business Registration" />

    <div class="min-h-screen bg-brand-light flex flex-col items-center justify-center px-4 py-12">
        <div class="w-full max-w-5xl">

            <!-- Header -->
            <div class="text-center mb-8 sm:mb-10">
                <div class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-brand-dark mb-4 sm:mb-5 shadow-soft-lg">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-dark tracking-tight">Business Registration</h1>
                <p class="text-brand-muted text-xs sm:text-sm mt-2">Set up your business profile and create the administrator account</p>
            </div>

            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">

                    <!-- Business Info -->
                    <div class="card p-4 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-brand-border">
                            <div class="w-8 h-8 rounded-xl bg-brand-lighter flex items-center justify-center">
                                <svg class="w-4 h-4 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-brand-dark text-sm">Business Details</p>
                                <p class="text-xs text-brand-muted">Company information</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="input-label">Shop / Company Name *</label>
                                <input type="text" v-model="form.shop_name" required autofocus class="input-field mt-1.5" placeholder="e.g. Mobile World" />
                                <InputError :message="form.errors.shop_name" />
                            </div>
                            <div>
                                <label class="input-label">Physical Address *</label>
                                <input type="text" v-model="form.address" required class="input-field mt-1.5" placeholder="123 Main Street..." />
                                <InputError :message="form.errors.address" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="input-label">Phone</label>
                                    <input type="text" v-model="form.phone" class="input-field mt-1.5" placeholder="+1 555 000..." />
                                    <InputError :message="form.errors.phone" />
                                </div>
                                <div>
                                    <label class="input-label">Currency *</label>
                                    <select v-model="form.currency" required class="input-field mt-1.5">
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                        <option value="GBP">GBP (£)</option>
                                        <option value="LKR">LKR (₨)</option>
                                        <option value="INR">INR (₹)</option>
                                    </select>
                                    <InputError :message="form.errors.currency" />
                                </div>
                            </div>
                            <div>
                                <label class="input-label">Business Email</label>
                                <input type="email" v-model="form.email" class="input-field mt-1.5" placeholder="hello@yourshop.com" />
                                <InputError :message="form.errors.email" />
                            </div>
                        </div>
                    </div>

                    <!-- Owner Info -->
                    <div class="card p-4 sm:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-brand-border">
                            <div class="w-8 h-8 rounded-xl bg-brand-lighter flex items-center justify-center">
                                <svg class="w-4 h-4 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-brand-dark text-sm">Administrator Account</p>
                                <p class="text-xs text-brand-muted">Owner login credentials</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="input-label">Full Name *</label>
                                <input type="text" v-model="form.owner_name" required class="input-field mt-1.5" placeholder="John Smith" />
                                <InputError :message="form.errors.owner_name" />
                            </div>
                            <div>
                                <label class="input-label">Login Email *</label>
                                <input type="email" v-model="form.owner_email" required class="input-field mt-1.5" placeholder="admin@yourshop.com" />
                                <InputError :message="form.errors.owner_email" />
                            </div>
                            <div>
                                <label class="input-label">Password *</label>
                                <input type="password" v-model="form.owner_password" required class="input-field mt-1.5" placeholder="Min. 8 characters" />
                                <InputError :message="form.errors.owner_password" />
                            </div>
                            <div>
                                <label class="input-label">Confirm Password *</label>
                                <input type="password" v-model="form.owner_password_confirmation" required class="input-field mt-1.5" placeholder="Repeat password" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary px-10 py-3 text-base" :disabled="form.processing" :class="{ 'opacity-60 cursor-not-allowed': form.processing }">
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        {{ form.processing ? 'Initializing...' : 'Complete Setup & Launch' }}
                        <svg v-if="!form.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>
