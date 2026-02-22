<script setup>
import { ref, computed, watchEffect } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const props = defineProps({
    isCollapsed: Boolean,
    isMobile: { type: Boolean, default: false },
});

const emit = defineEmits(['toggle-sidebar', 'close-mobile']);

const page = usePage();
const business   = computed(() => page.props.business);
const isOwner    = computed(() => page.props.auth?.user?.roles?.includes('Owner'));
const appVersion = computed(() => page.props.appVersion ?? 'v1.0.0');

const openMenu = ref(null);

watchEffect(() => {
    const url = page.url;
    if (url.includes('/admin/users') || url.includes('/admin/roles')) {
        openMenu.value = 'management';
    } else if (url.includes('/admin/settings')) {
        openMenu.value = 'settings';
    }
});

const toggleMenu = (name) => {
    openMenu.value = openMenu.value === name ? null : name;
};

const isActive = (patterns) => {
    return patterns.some(p => route().current(p));
};

// Close mobile sidebar on navigation
router.on('navigate', () => {
    if (props.isMobile) {
        emit('close-mobile');
    }
});
</script>

<template>
    <aside
        class="flex flex-col h-screen shrink-0 bg-brand-dark text-white transition-all duration-300 ease-in-out z-30"
        :class="isCollapsed ? 'w-[72px]' : 'w-64'"
    >

        <!-- Brand / Logo -->
        <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10 overflow-hidden relative">
            <div v-if="business?.logo" class="w-9 h-9 rounded-xl overflow-hidden shrink-0 bg-white/10">
                <img :src="business.logo" alt="Logo" class="w-full h-full object-contain" />
            </div>
            <div v-else class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <Transition
                enter-active-class="transition duration-200 delay-100"
                enter-from-class="opacity-0 -translate-x-2"
                enter-to-class="opacity-100 translate-x-0"
                leave-active-class="transition duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="!isCollapsed" class="min-w-0 flex-1">
                    <p class="font-bold text-sm leading-tight truncate">{{ business?.shop_name ?? 'POS System' }}</p>
                    <p class="text-white/40 text-[11px] mt-0.5">Management Suite</p>
                </div>
            </Transition>

            <!-- Close button for mobile / Collapse for desktop -->
            <button
                v-if="isMobile"
                @click="emit('close-mobile')"
                class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors shrink-0"
            >
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <button
                v-else
                @click="emit('toggle-sidebar')"
                class="w-6 h-6 rounded-md bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors shrink-0"
                :title="isCollapsed ? 'Expand' : 'Collapse'"
            >
                <svg v-if="isCollapsed" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                <svg v-else class="w-3.5 h-3.5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M19 19l-7-7 7-7"/></svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-3 px-2.5 space-y-0.5">

            <Link
                :href="route('dashboard')"
                :class="[
                    'sidebar-item',
                    route().current('dashboard') ? 'sidebar-item-active' : ''
                ]"
                :title="isCollapsed ? 'Dashboard' : ''"
            >
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span v-if="!isCollapsed" class="whitespace-nowrap overflow-hidden">Dashboard</span>
            </Link>

            <template v-if="isOwner">
                <div>
                    <button
                        @click="toggleMenu('management')"
                        :class="[
                            'sidebar-item w-full',
                            isActive(['admin.users.*', 'admin.roles.*']) ? 'sidebar-item-active' : ''
                        ]"
                        :title="isCollapsed ? 'Management' : ''"
                    >
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span v-if="!isCollapsed" class="flex-1 text-left whitespace-nowrap overflow-hidden">Management</span>
                        <svg v-if="!isCollapsed" class="w-4 h-4 text-white/40 transition-transform duration-200" :class="openMenu === 'management' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <Transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-40"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 max-h-40"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-show="openMenu === 'management' && !isCollapsed" class="ml-4 mt-0.5 space-y-0.5 overflow-hidden">
                            <Link
                                :href="route('admin.users.index')"
                                :class="['sidebar-subitem', route().current('admin.users.*') ? 'sidebar-subitem-active' : '']"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60 shrink-0"></span>
                                Staff Management
                            </Link>
                            <Link
                                :href="route('admin.roles.index')"
                                :class="['sidebar-subitem', route().current('admin.roles.*') ? 'sidebar-subitem-active' : '']"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60 shrink-0"></span>
                                Roles & Permissions
                            </Link>
                        </div>
                    </Transition>
                </div>

                <div>
                    <button
                        @click="toggleMenu('settings')"
                        :class="[
                            'sidebar-item w-full',
                            isActive(['admin.settings.*']) ? 'sidebar-item-active' : ''
                        ]"
                        :title="isCollapsed ? 'Settings' : ''"
                    >
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span v-if="!isCollapsed" class="flex-1 text-left whitespace-nowrap overflow-hidden">Settings</span>
                        <svg v-if="!isCollapsed" class="w-4 h-4 text-white/40 transition-transform duration-200" :class="openMenu === 'settings' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <Transition
                        enter-active-class="transition-all duration-200 ease-out"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-40"
                        leave-active-class="transition-all duration-150 ease-in"
                        leave-from-class="opacity-100 max-h-40"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-show="openMenu === 'settings' && !isCollapsed" class="ml-4 mt-0.5 space-y-0.5 overflow-hidden">
                            <Link
                                :href="route('admin.settings.index')"
                                :class="['sidebar-subitem', route().current('admin.settings.*') ? 'sidebar-subitem-active' : '']"
                            >
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60 shrink-0"></span>
                                Business Settings
                            </Link>
                        </div>
                    </Transition>
                </div>
            </template>

        </nav>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-white/10 overflow-hidden">
            <div class="flex items-center gap-2">
                <span v-if="!isCollapsed" class="text-[10px] text-white/30 whitespace-nowrap">{{ appVersion }}</span>
                <span v-if="!isCollapsed" class="text-white/20">·</span>
                <a href="https://milanmadusanka.me/" target="_blank" class="text-[10px] text-white/30 hover:text-white/60 transition-colors whitespace-nowrap flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    <span v-if="!isCollapsed">milanmadusanka.me</span>
                </a>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.sidebar-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 500;
    color: rgba(255,255,255,0.55);
    transition: all 150ms;
    cursor: pointer;
    text-decoration: none;
}
.sidebar-item:hover {
    color: rgba(255,255,255,0.95);
    background: rgba(255,255,255,0.08);
}
.sidebar-item-active {
    background: rgba(255,255,255,0.12);
    color: rgba(255,255,255,1);
    font-weight: 600;
}
.sidebar-icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}
.sidebar-subitem {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 12px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    color: rgba(255,255,255,0.45);
    transition: all 150ms;
    cursor: pointer;
    text-decoration: none;
}
.sidebar-subitem:hover {
    color: rgba(255,255,255,0.9);
    background: rgba(255,255,255,0.08);
}
.sidebar-subitem-active {
    background: rgba(255,255,255,0.10);
    color: rgba(255,255,255,1);
    font-weight: 600;
}
</style>

