<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps({
    isSidebarCollapsed: Boolean
});

const emit = defineEmits(['toggle-sidebar']);

const page = usePage();
const user     = computed(() => page.props.auth?.user);
const business = computed(() => page.props.business);
const currentLocale = computed(() => page.props.locale ?? 'en');

// Language options
const languages = [
    { code: 'en', label: 'English',   flag: 'EN' },
    { code: 'si', label: 'සිංහල',     flag: 'SI' },
    { code: 'ta', label: 'தமிழ்',      flag: 'TA' },
];

const currentLang = computed(() => languages.find(l => l.code === currentLocale.value) || languages[0]);

const switchLanguage = (code) => {
    router.post(route('language.switch'), { locale: code }, { preserveState: false, preserveScroll: true });
};

// Clock logic
const now = ref(new Date());
let clockTimer = null;

const timeString = computed(() => {
    const tz = business.value?.timezone ?? 'UTC';
    return now.value.toLocaleTimeString('en-US', { timeZone: tz, hour: '2-digit', minute: '2-digit', second: '2-digit' });
});
const dateString = computed(() => {
    const tz = business.value?.timezone ?? 'UTC';
    return now.value.toLocaleDateString('en-US', { timeZone: tz, weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
});
const tzLabel = computed(() => business.value?.timezone ?? 'UTC');

onMounted(() => { clockTimer = setInterval(() => now.value = new Date(), 1000); });
onUnmounted(() => clearInterval(clockTimer));
</script>

<template>
    <header class="h-14 sm:h-16 glass sticky top-0 z-20 flex items-center px-3 sm:px-6 gap-2 sm:gap-4 border-b border-brand-border">

        <!-- Sidebar Toggle Button -->
        <button
            @click="emit('toggle-sidebar')"
            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl border border-brand-border bg-white flex items-center justify-center text-brand-dark hover:bg-brand-light transition-all duration-200"
            title="Toggle Sidebar"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
        </button>

        <!-- Spacer -->
        <div class="flex-1"></div>

        <!-- Live Clock -->
        <div class="hidden md:flex flex-col items-end mr-2 shrink-0">
            <span class="font-mono text-sm font-bold text-brand-dark tabular-nums">{{ timeString }}</span>
            <span class="text-[11px] text-brand-muted">{{ dateString }} &middot; {{ tzLabel }}</span>
        </div>

        <!-- Language Switcher -->
        <Dropdown align="right">
            <template #trigger>
                <button class="flex items-center gap-1.5 rounded-xl px-2.5 py-1.5 sm:py-2 border border-brand-border bg-white hover:bg-brand-light shadow-soft-xs transition-all duration-150 text-xs sm:text-sm font-semibold text-brand-dark">
                    <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    <span class="hidden sm:inline">{{ currentLang.flag }}</span>
                </button>
            </template>
            <template #content>
                <div class="py-1">
                    <button
                        v-for="lang in languages"
                        :key="lang.code"
                        @click="switchLanguage(lang.code)"
                        :class="[
                            'w-full text-left px-3 py-2 text-sm transition-colors flex items-center gap-2',
                            currentLocale === lang.code
                                ? 'bg-brand-light font-semibold text-brand-dark'
                                : 'text-brand-accent hover:bg-brand-light hover:text-brand-dark'
                        ]"
                    >
                        <span class="w-6 text-center text-xs font-bold text-brand-muted">{{ lang.flag }}</span>
                        {{ lang.label }}
                        <svg v-if="currentLocale === lang.code" class="w-3.5 h-3.5 ml-auto text-brand-dark" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
            </template>
        </Dropdown>

        <!-- User Menu -->
        <Dropdown align="right">
            <template #trigger>
                <button class="flex items-center gap-2 sm:gap-2.5 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 border border-brand-border bg-white hover:bg-brand-light shadow-soft-xs transition-all duration-150">
                    <div class="w-7 h-7 rounded-lg bg-brand-dark text-white flex items-center justify-center text-xs font-bold select-none shrink-0">
                        {{ user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-semibold text-brand-dark leading-tight">{{ user?.name }}</p>
                        <p class="text-[11px] text-brand-muted leading-tight">{{ user?.roles?.[0] ?? 'User' }}</p>
                    </div>
                    <svg class="w-3.5 h-3.5 text-brand-muted shrink-0 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </template>
            <template #content>
                <div class="px-3 py-2 border-b border-brand-border mb-1">
                    <p class="text-xs font-bold text-brand-dark truncate">{{ user?.name }}</p>
                    <p class="text-[11px] text-brand-muted truncate">{{ user?.email }}</p>
                </div>
                <DropdownLink :href="route('logout')" method="post" as="button">
                    <svg class="w-4 h-4 shrink-0 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </DropdownLink>
            </template>
        </Dropdown>

    </header>
</template>
