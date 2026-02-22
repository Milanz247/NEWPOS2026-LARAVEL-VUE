<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, router, usePage, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import { confirmDelete } from '@/plugins/alert.js';
import { useTranslation } from '@/lib/useTranslation.js';

const { t } = useTranslation();

const props = defineProps({
    settings:  Object,
    timezones: Array,
    locations: Object,   // paginated object
});

// ── Tab Navigation ───────────────────────────
const activeTab = ref('profile');
const tabs = [
    { id: 'profile',   label: 'business_profile',    icon: 'building' },
    { id: 'financial', label: 'financials',           icon: 'currency' },
    { id: 'system',    label: 'system_settings',      icon: 'cog' },
    { id: 'locations', label: 'business_locations',   icon: 'location' },
];

// ── Business Details Form ────────────────────
const detailForm = useForm({
    name:     props.settings?.name ?? '',
    currency: props.settings?.currency ?? 'USD',
    timezone: props.settings?.timezone ?? 'UTC',
});

const saveDetails = () => {
    detailForm.put(route('admin.settings.update'));
};

// ── Financial Form ───────────────────────────
const financialForm = useForm({
    currency_symbol:           props.settings?.currency_symbol ?? '$',
    currency_symbol_placement: props.settings?.currency_symbol_placement ?? 'before',
    tax_number:                props.settings?.tax_number ?? '',
    tax_percentage:            props.settings?.tax_percentage ?? 0,
    default_profit_percent:    props.settings?.default_profit_percent ?? 25,
});

const saveFinancials = () => {
    financialForm.put(route('admin.settings.financials'));
};

// ── System Settings Form ─────────────────────
const systemForm = useForm({
    start_date:              props.settings?.start_date ? props.settings.start_date.substring(0, 10) : '',
    fy_start_month:          props.settings?.fy_start_month ?? 1,
    stock_accounting_method: props.settings?.stock_accounting_method ?? 'FIFO',
    transaction_edit_days:   props.settings?.transaction_edit_days ?? 30,
    date_format:             props.settings?.date_format ?? 'm/d/Y',
    time_format:             props.settings?.time_format ?? '12',
    currency_precision:      props.settings?.currency_precision ?? 2,
    quantity_precision:      props.settings?.quantity_precision ?? 2,
    toast_position:          props.settings?.toast_position ?? 'top-right',
});

const saveSystem = () => {
    systemForm.put(route('admin.settings.system'));
};

const months = [
    { value: 1,  label: 'January' },
    { value: 2,  label: 'February' },
    { value: 3,  label: 'March' },
    { value: 4,  label: 'April' },
    { value: 5,  label: 'May' },
    { value: 6,  label: 'June' },
    { value: 7,  label: 'July' },
    { value: 8,  label: 'August' },
    { value: 9,  label: 'September' },
    { value: 10, label: 'October' },
    { value: 11, label: 'November' },
    { value: 12, label: 'December' },
];

const dateFormats = [
    { value: 'm/d/Y', label: 'MM/DD/YYYY (01/31/2026)' },
    { value: 'd/m/Y', label: 'DD/MM/YYYY (31/01/2026)' },
    { value: 'Y-m-d', label: 'YYYY-MM-DD (2026-01-31)' },
    { value: 'd-m-Y', label: 'DD-MM-YYYY (31-01-2026)' },
    { value: 'd.m.Y', label: 'DD.MM.YYYY (31.01.2026)' },
];

const toastPositions = [
    { value: 'top-right',     label: 'Top Right' },
    { value: 'top-left',      label: 'Top Left' },
    { value: 'bottom-right',  label: 'Bottom Right' },
    { value: 'bottom-left',   label: 'Bottom Left' },
    { value: 'top-center',    label: 'Top Center' },
    { value: 'bottom-center', label: 'Bottom Center' },
];

// ── Logo Upload Form ─────────────────────────
const logoForm    = useForm({ logo: null });
const logoPreview = ref(props.settings?.logo ? (usePage().props.business?.logo ?? null) : null);
const fileInput   = ref(null);

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    logoForm.logo = file;
    const reader = new FileReader();
    reader.onload = (ev) => logoPreview.value = ev.target.result;
    reader.readAsDataURL(file);
};

const uploadLogo = () => {
    logoForm.post(route('admin.settings.logo'), {
        forceFormData: true,
        onSuccess: () => fileInput.value && (fileInput.value.value = ''),
    });
};

const deleteLogo = () => {
    if (confirm('Remove the current logo?')) {
        router.delete(route('admin.settings.logo.delete'));
        logoPreview.value = null;
    }
};

// ── Timezone search filter ───────────────────
const tzSearch = ref('');
const filteredTimezones = computed(() => {
    if (!tzSearch.value) return props.timezones.slice(0, 80);
    const q = tzSearch.value.toLowerCase();
    return props.timezones.filter(t => t.toLowerCase().includes(q)).slice(0, 80);
});

// ── Location Management ──────────────────────
const showLocationModal = ref(false);
const editingLocation   = ref(null);

const locationForm = useForm({
    name:             '',
    landmark:         '',
    city:             '',
    zip_code:         '',
    state:            '',
    country:          '',
    mobile:           [''],
    alternate_number: '',
    email:            '',
    website:          '',
    is_active:        true,
});

const openAddLocation = () => {
    editingLocation.value = null;
    locationForm.reset();
    locationForm.mobile = [''];
    locationForm.is_active = true;
    showLocationModal.value = true;
};

const openEditLocation = (loc) => {
    editingLocation.value = loc;
    locationForm.name             = loc.name;
    locationForm.landmark         = loc.landmark ?? '';
    locationForm.city             = loc.city ?? '';
    locationForm.zip_code         = loc.zip_code ?? '';
    locationForm.state            = loc.state ?? '';
    locationForm.country          = loc.country ?? '';
    locationForm.mobile           = loc.mobile && loc.mobile.length > 0 ? [...loc.mobile] : [''];
    locationForm.alternate_number = loc.alternate_number ?? '';
    locationForm.email            = loc.email ?? '';
    locationForm.website          = loc.website ?? '';
    locationForm.is_active        = loc.is_active;
    showLocationModal.value = true;
};

const addMobileField = () => {
    locationForm.mobile.push('');
};

const removeMobileField = (index) => {
    if (locationForm.mobile.length > 1) {
        locationForm.mobile.splice(index, 1);
    }
};

const saveLocation = () => {
    if (editingLocation.value) {
        locationForm.put(route('admin.locations.update', editingLocation.value.id), {
            onSuccess: () => { showLocationModal.value = false; },
        });
    } else {
        locationForm.post(route('admin.locations.store'), {
            onSuccess: () => { showLocationModal.value = false; },
        });
    }
};

const deleteLocation = async (loc) => {
    const ok = await confirmDelete({
        title: `Delete "${loc.name}"?`,
        text:  loc.users_count > 0
            ? `This location has ${loc.users_count} assigned user(s). Reassign them first.`
            : 'This location will be permanently removed.',
    });
    if (ok) {
        router.delete(route('admin.locations.destroy', loc.id));
    }
};
</script>

<template>
    <Head title="Business Settings" />

    <AdminLayout>
        <div class="page-header mb-4 sm:mb-6">
            <div>
                <h1 class="page-title">{{ t('business_settings') }}</h1>
                <p class="text-xs sm:text-sm text-brand-muted mt-0.5">{{ t('manage_business_profile') }}</p>
            </div>
        </div>

        <!-- ── Tab Navigation ──────────────── -->
        <div class="flex gap-1 mb-4 sm:mb-6 border-b border-brand-border overflow-x-auto">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="activeTab = tab.id"
                :class="[
                    'px-3 sm:px-4 py-2.5 text-xs sm:text-sm font-semibold whitespace-nowrap transition-all duration-200 border-b-2 -mb-px',
                    activeTab === tab.id
                        ? 'border-brand-dark text-brand-dark'
                        : 'border-transparent text-brand-muted hover:text-brand-dark hover:border-brand-border'
                ]"
            >
                {{ t(tab.label) }}
            </button>
        </div>

        <!-- ═══════════════════════════════════ -->
        <!-- TAB 1: Business Profile             -->
        <!-- ═══════════════════════════════════ -->
        <div v-show="activeTab === 'profile'">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

                <!-- Left: Business Details -->
                <div class="lg:col-span-2 card p-4 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-brand-border">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background: #F1F5F9;">
                            <svg class="w-4 h-4 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-brand-dark text-sm">{{ t('business_info') }}</p>
                            <p class="text-xs text-brand-muted">{{ t('business_info') }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="saveDetails" class="space-y-5">
                        <div>
                            <label class="input-label">{{ t('business_name') }} *</label>
                            <input type="text" v-model="detailForm.name" required class="input-field mt-1.5" />
                            <InputError :message="detailForm.errors.name" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">{{ t('currency') }} *</label>
                                <select v-model="detailForm.currency" required class="input-field mt-1.5">
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                    <option value="LKR">LKR (₨)</option>
                                    <option value="INR">INR (₹)</option>
                                </select>
                                <InputError :message="detailForm.errors.currency" />
                            </div>
                            <div>
                                <label class="input-label">{{ t('timezone') }} *</label>
                                <input v-model="tzSearch" :placeholder="t('search') + '...'" class="input-field mt-1.5 mb-1.5" />
                                <select v-model="detailForm.timezone" required class="input-field" size="4">
                                    <option v-for="tz in filteredTimezones" :key="tz" :value="tz">{{ tz }}</option>
                                </select>
                                <p class="text-xs text-brand-muted mt-1">Selected: <strong>{{ detailForm.timezone }}</strong></p>
                                <InputError :message="detailForm.errors.timezone" />
                            </div>
                        </div>

                        <div class="pt-4 border-t border-brand-border flex justify-end">
                            <button type="submit" class="btn-primary" :disabled="detailForm.processing">
                                {{ detailForm.processing ? t('saving') : t('save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Logo Upload -->
                <div class="card p-6 flex flex-col">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-brand-border">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background: #F1F5F9;">
                            <svg class="w-4 h-4 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-brand-dark text-sm">{{ t('logo') }}</p>
                            <p class="text-xs text-brand-muted">PNG, JPG, SVG (max 2MB)</p>
                        </div>
                    </div>

                    <div class="flex-1 flex items-center justify-center mb-6">
                        <div class="w-36 h-36 rounded-2xl border-2 border-dashed border-brand-border flex items-center justify-center bg-brand-light overflow-hidden">
                            <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-contain p-2" alt="Preview" />
                            <div v-else class="text-center">
                                <svg class="w-10 h-10 text-brand-border mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-xs text-brand-muted mt-2">{{ t('no_logo') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <input ref="fileInput" type="file" @change="onFileChange" accept="image/*" class="hidden" id="logo-upload" />
                        <label for="logo-upload" class="btn-secondary w-full justify-center cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            {{ t('choose_file') }}
                        </label>
                        <InputError :message="logoForm.errors.logo" />
                        <button v-if="logoForm.logo" @click="uploadLogo" class="btn-primary w-full justify-center" :disabled="logoForm.processing">
                            {{ logoForm.processing ? t('uploading') : t('upload_logo') }}
                        </button>
                        <button v-if="logoPreview && !logoForm.logo" @click="deleteLogo" class="btn-danger w-full justify-center text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            {{ t('remove_logo') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════ -->
        <!-- TAB 2: Financials                   -->
        <!-- ═══════════════════════════════════ -->
        <div v-show="activeTab === 'financial'">
            <div class="max-w-2xl">
                <div class="card p-4 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-brand-border">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background: #F0FDF4;">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-brand-dark text-sm">{{ t('financial_info') }}</p>
                            <p class="text-xs text-brand-muted">{{ t('financial_info') }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="saveFinancials" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">{{ t('currency_symbol') }} *</label>
                                <input type="text" v-model="financialForm.currency_symbol" required class="input-field mt-1.5" placeholder="$" />
                                <InputError :message="financialForm.errors.currency_symbol" />
                            </div>
                            <div>
                                <label class="input-label">{{ t('symbol_placement') }} *</label>
                                <select v-model="financialForm.currency_symbol_placement" required class="input-field mt-1.5">
                                    <option value="before">{{ t('before_amount') }} ($100)</option>
                                    <option value="after">{{ t('after_amount') }} (100$)</option>
                                </select>
                                <InputError :message="financialForm.errors.currency_symbol_placement" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">{{ t('tax_number') }}</label>
                                <input type="text" v-model="financialForm.tax_number" class="input-field mt-1.5" placeholder="e.g. VAT-12345678" />
                                <InputError :message="financialForm.errors.tax_number" />
                            </div>
                            <div>
                                <label class="input-label">{{ t('tax_percentage') }} (%) *</label>
                                <input type="number" step="0.01" min="0" max="100" v-model="financialForm.tax_percentage" required class="input-field mt-1.5" />
                                <InputError :message="financialForm.errors.tax_percentage" />
                            </div>
                        </div>

                        <div>
                            <label class="input-label">{{ t('default_profit_percent') }} (%) *</label>
                            <input type="number" step="0.01" min="0" max="100" v-model="financialForm.default_profit_percent" required class="input-field mt-1.5" />
                            <InputError :message="financialForm.errors.default_profit_percent" />
                        </div>

                        <div class="pt-4 border-t border-brand-border flex justify-end">
                            <button type="submit" class="btn-primary" :disabled="financialForm.processing">
                                {{ financialForm.processing ? t('saving') : t('save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════ -->
        <!-- TAB 3: System Settings              -->
        <!-- ═══════════════════════════════════ -->
        <div v-show="activeTab === 'system'">
            <div class="max-w-2xl">
                <div class="card p-4 sm:p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-brand-border">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background: #EFF6FF;">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-brand-dark text-sm">{{ t('system_settings') }}</p>
                            <p class="text-xs text-brand-muted">{{ t('system_settings_desc') }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="saveSystem" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">{{ t('business_start_date') }}</label>
                                <input type="date" v-model="systemForm.start_date" class="input-field mt-1.5" />
                                <InputError :message="systemForm.errors.start_date" />
                            </div>
                            <div>
                                <label class="input-label">{{ t('fy_start_month') }} *</label>
                                <select v-model="systemForm.fy_start_month" required class="input-field mt-1.5">
                                    <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                                </select>
                                <InputError :message="systemForm.errors.fy_start_month" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">{{ t('stock_accounting_method') }} *</label>
                                <select v-model="systemForm.stock_accounting_method" required class="input-field mt-1.5">
                                    <option value="FIFO">FIFO (First In, First Out)</option>
                                    <option value="LIFO">LIFO (Last In, First Out)</option>
                                </select>
                                <InputError :message="systemForm.errors.stock_accounting_method" />
                            </div>
                            <div>
                                <label class="input-label">{{ t('transaction_edit_days') }} *</label>
                                <input type="number" min="0" max="365" v-model="systemForm.transaction_edit_days" required class="input-field mt-1.5" />
                                <p class="text-xs text-brand-muted mt-1">{{ t('transaction_edit_days_hint') }}</p>
                                <InputError :message="systemForm.errors.transaction_edit_days" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">{{ t('date_format') }} *</label>
                                <select v-model="systemForm.date_format" required class="input-field mt-1.5">
                                    <option v-for="f in dateFormats" :key="f.value" :value="f.value">{{ f.label }}</option>
                                </select>
                                <InputError :message="systemForm.errors.date_format" />
                            </div>
                            <div>
                                <label class="input-label">{{ t('time_format') }} *</label>
                                <select v-model="systemForm.time_format" required class="input-field mt-1.5">
                                    <option value="12">12 {{ t('hour') }} (1:30 PM)</option>
                                    <option value="24">24 {{ t('hour') }} (13:30)</option>
                                </select>
                                <InputError :message="systemForm.errors.time_format" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="input-label">{{ t('currency_precision') }} *</label>
                                <select v-model="systemForm.currency_precision" required class="input-field mt-1.5">
                                    <option :value="0">0 (100)</option>
                                    <option :value="1">1 (100.0)</option>
                                    <option :value="2">2 (100.00)</option>
                                    <option :value="3">3 (100.000)</option>
                                    <option :value="4">4 (100.0000)</option>
                                </select>
                                <InputError :message="systemForm.errors.currency_precision" />
                            </div>
                            <div>
                                <label class="input-label">{{ t('quantity_precision') }} *</label>
                                <select v-model="systemForm.quantity_precision" required class="input-field mt-1.5">
                                    <option :value="0">0 (100)</option>
                                    <option :value="1">1 (100.0)</option>
                                    <option :value="2">2 (100.00)</option>
                                    <option :value="3">3 (100.000)</option>
                                    <option :value="4">4 (100.0000)</option>
                                </select>
                                <InputError :message="systemForm.errors.quantity_precision" />
                            </div>
                        </div>

                        <div>
                            <label class="input-label">{{ t('toast_position') }} *</label>
                            <select v-model="systemForm.toast_position" required class="input-field mt-1.5">
                                <option v-for="pos in toastPositions" :key="pos.value" :value="pos.value">{{ pos.label }}</option>
                            </select>
                            <p class="text-xs text-brand-muted mt-1">{{ t('toast_position_hint') ?? 'Choose where notification popups appear' }}</p>
                            <InputError :message="systemForm.errors.toast_position" />
                        </div>

                        <div class="pt-4 border-t border-brand-border flex justify-end">
                            <button type="submit" class="btn-primary" :disabled="systemForm.processing">
                                {{ systemForm.processing ? t('saving') : t('save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ═══════════════════════════════════ -->
        <!-- TAB 4: Business Locations           -->
        <!-- ═══════════════════════════════════ -->
        <div v-show="activeTab === 'locations'">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="font-bold text-brand-dark text-sm">{{ t('location_info') }}</p>
                </div>
                <button @click="openAddLocation" class="btn-primary shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">{{ t('add_location') }}</span>
                    <span class="sm:hidden">{{ t('new') }}</span>
                </button>
            </div>

            <!-- Desktop Table -->
            <div class="card overflow-hidden hidden sm:block">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ t('location_id') }}</th>
                                <th>{{ t('name') }}</th>
                                <th class="hidden md:table-cell">{{ t('city') }}</th>
                                <th class="hidden md:table-cell">{{ t('mobile') }}</th>
                                <th>{{ t('status') }}</th>
                                <th>{{ t('users') }}</th>
                                <th class="text-right">{{ t('actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="loc in locations.data" :key="loc.id">
                                <td>
                                    <span class="badge badge-default font-mono">{{ loc.location_id }}</span>
                                </td>
                                <td class="font-semibold text-brand-dark">{{ loc.name }}</td>
                                <td class="hidden md:table-cell text-brand-accent">{{ loc.city || '—' }}</td>
                                <td class="hidden md:table-cell text-brand-accent">
                                    <span v-if="loc.mobile && loc.mobile.length > 0">{{ loc.mobile[0] }}<span v-if="loc.mobile.length > 1" class="text-brand-muted"> +{{ loc.mobile.length - 1 }}</span></span>
                                    <span v-else>—</span>
                                </td>
                                <td>
                                    <span :class="loc.is_active ? 'badge badge-success' : 'badge badge-danger'">
                                        {{ loc.is_active ? t('active') : t('inactive') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-default">{{ loc.users_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEditLocation(loc)" class="btn-secondary text-xs py-1.5 px-3">{{ t('edit') }}</button>
                                        <button @click="deleteLocation(loc)" class="btn-danger text-xs py-1.5 px-3">{{ t('delete') }}</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!locations.data || locations.data.length === 0">
                                <td colspan="7" class="text-center py-12 text-brand-muted">{{ t('no_locations') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="locations.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-brand-border">
                    <p class="text-xs text-brand-muted">
                        {{ t('showing') }} {{ locations.from }}–{{ locations.to }} {{ t('of') }} {{ locations.total }}
                    </p>
                    <div class="flex gap-1">
                        <template v-for="(link, i) in locations.links" :key="i">
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
                <div v-for="loc in locations.data" :key="loc.id" class="card p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="badge badge-default font-mono text-xs">{{ loc.location_id }}</span>
                                <span :class="loc.is_active ? 'badge badge-success' : 'badge badge-danger'" class="text-xs">
                                    {{ loc.is_active ? t('active') : t('inactive') }}
                                </span>
                            </div>
                            <p class="font-semibold text-brand-dark text-sm">{{ loc.name }}</p>
                            <p v-if="loc.city" class="text-xs text-brand-muted">{{ loc.city }}</p>
                        </div>
                        <span class="badge badge-default text-xs shrink-0">{{ loc.users_count ?? 0 }} {{ t('users') }}</span>
                    </div>
                    <div class="flex gap-2 mt-3 pt-3 border-t border-brand-border">
                        <button @click="openEditLocation(loc)" class="btn-secondary text-xs py-1.5 px-3 flex-1">{{ t('edit') }}</button>
                        <button @click="deleteLocation(loc)" class="btn-danger text-xs py-1.5 px-3 flex-1">{{ t('delete') }}</button>
                    </div>
                </div>

                <div v-if="!locations.data || locations.data.length === 0" class="card p-8 text-center text-brand-muted text-sm">
                    {{ t('no_locations') }}
                </div>

                <!-- Mobile Pagination -->
                <div v-if="locations.last_page > 1" class="flex items-center justify-between">
                    <Link
                        v-if="locations.prev_page_url"
                        :href="locations.prev_page_url"
                        class="btn-secondary text-xs py-1.5 px-4"
                        preserve-state preserve-scroll
                    >← {{ t('previous') }}</Link>
                    <span v-else />
                    <span class="text-xs text-brand-muted">{{ locations.current_page }} / {{ locations.last_page }}</span>
                    <Link
                        v-if="locations.next_page_url"
                        :href="locations.next_page_url"
                        class="btn-secondary text-xs py-1.5 px-4"
                        preserve-state preserve-scroll
                    >{{ t('next') }} →</Link>
                    <span v-else />
                </div>
            </div>
        </div>

        <!-- ── Location Modal ──────────────── -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-200"
                enter-from-class="opacity-0"
                leave-active-class="transition-opacity duration-200"
                leave-to-class="opacity-0"
            >
                <div v-if="showLocationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="showLocationModal = false">
                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showLocationModal = false" />
                    <div class="relative bg-white rounded-2xl shadow-soft-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 sm:p-8 z-10">
                        <h3 class="text-lg font-bold text-brand-dark mb-6">
                            {{ editingLocation ? t('edit_location') : t('add_location') }}
                            <span v-if="editingLocation" class="text-sm font-normal text-brand-muted ml-2">({{ editingLocation.location_id }})</span>
                        </h3>

                        <form @submit.prevent="saveLocation" class="space-y-4">
                            <div>
                                <label class="input-label">{{ t('name') }} *</label>
                                <input type="text" v-model="locationForm.name" required class="input-field mt-1.5" :placeholder="t('location_name_placeholder')" />
                                <InputError :message="locationForm.errors.name" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="input-label">{{ t('landmark') }}</label>
                                    <input type="text" v-model="locationForm.landmark" class="input-field mt-1.5" />
                                    <InputError :message="locationForm.errors.landmark" />
                                </div>
                                <div>
                                    <label class="input-label">{{ t('city') }}</label>
                                    <input type="text" v-model="locationForm.city" class="input-field mt-1.5" />
                                    <InputError :message="locationForm.errors.city" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="input-label">{{ t('zip_code') }}</label>
                                    <input type="text" v-model="locationForm.zip_code" class="input-field mt-1.5" />
                                    <InputError :message="locationForm.errors.zip_code" />
                                </div>
                                <div>
                                    <label class="input-label">{{ t('state') }}</label>
                                    <input type="text" v-model="locationForm.state" class="input-field mt-1.5" />
                                    <InputError :message="locationForm.errors.state" />
                                </div>
                                <div>
                                    <label class="input-label">{{ t('country') }}</label>
                                    <input type="text" v-model="locationForm.country" class="input-field mt-1.5" />
                                    <InputError :message="locationForm.errors.country" />
                                </div>
                            </div>

                            <!-- Mobile Numbers (dynamic) -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="input-label">{{ t('mobile_numbers') }}</label>
                                    <button type="button" @click="addMobileField" class="text-xs text-brand-dark font-semibold hover:underline">+ {{ t('add_number') }}</button>
                                </div>
                                <div v-for="(num, index) in locationForm.mobile" :key="index" class="flex gap-2 mb-2">
                                    <input type="text" v-model="locationForm.mobile[index]" class="input-field flex-1" :placeholder="t('mobile_placeholder')" />
                                    <button v-if="locationForm.mobile.length > 1" type="button" @click="removeMobileField(index)" class="w-9 h-9 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <InputError :message="locationForm.errors.mobile" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="input-label">{{ t('alternate_number') }}</label>
                                    <input type="text" v-model="locationForm.alternate_number" class="input-field mt-1.5" />
                                    <InputError :message="locationForm.errors.alternate_number" />
                                </div>
                                <div>
                                    <label class="input-label">{{ t('email') }}</label>
                                    <input type="email" v-model="locationForm.email" class="input-field mt-1.5" />
                                    <InputError :message="locationForm.errors.email" />
                                </div>
                            </div>

                            <div>
                                <label class="input-label">{{ t('website') }}</label>
                                <input type="text" v-model="locationForm.website" class="input-field mt-1.5" placeholder="https://..." />
                                <InputError :message="locationForm.errors.website" />
                            </div>

                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" v-model="locationForm.is_active" class="rounded border-gray-300 text-brand-dark focus:ring-brand-dark" />
                                    <span class="text-sm font-medium text-brand-dark">{{ t('active') }}</span>
                                </label>
                            </div>

                            <div class="pt-4 border-t border-brand-border flex justify-end gap-3">
                                <button type="button" @click="showLocationModal = false" class="btn-secondary">{{ t('cancel') }}</button>
                                <button type="submit" class="btn-primary" :disabled="locationForm.processing">
                                    {{ locationForm.processing ? t('saving') : t('save_changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AdminLayout>
</template>
