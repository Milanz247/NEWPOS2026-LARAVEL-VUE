<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AppSidebar       from '@/Components/AppSidebar.vue';
import AppHeader        from '@/Components/AppHeader.vue';
import AppFooter        from '@/Components/AppFooter.vue';
import AppToast         from '@/Components/AppToast.vue';
import AppNotifications from '@/Components/AppNotifications.vue';

const isSidebarCollapsed = ref(false);
const isMobileSidebarOpen = ref(false);
const isMobile = ref(false);

const checkMobile = () => {
    isMobile.value = window.innerWidth < 1024;
    if (isMobile.value) {
        isSidebarCollapsed.value = true;
    }
};

const toggleSidebar = () => {
    if (isMobile.value) {
        isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
    } else {
        isSidebarCollapsed.value = !isSidebarCollapsed.value;
    }
};

const closeMobileSidebar = () => {
    isMobileSidebarOpen.value = false;
};

onMounted(() => {
    checkMobile();
    window.addEventListener('resize', checkMobile);
});
onUnmounted(() => {
    window.removeEventListener('resize', checkMobile);
});
</script>

<template>
    <div class="flex h-screen bg-brand-light overflow-hidden">

        <!-- Mobile Overlay -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isMobile && isMobileSidebarOpen"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
                @click="closeMobileSidebar"
            />
        </Transition>

        <!-- Sidebar -->
        <div
            :class="[
                isMobile ? 'fixed inset-y-0 left-0 z-50 transition-transform duration-300 lg:relative lg:translate-x-0' : '',
                isMobile && !isMobileSidebarOpen ? '-translate-x-full' : 'translate-x-0',
            ]"
        >
            <AppSidebar
                :is-collapsed="isMobile ? false : isSidebarCollapsed"
                :is-mobile="isMobile"
                @toggle-sidebar="toggleSidebar"
                @close-mobile="closeMobileSidebar"
            />
        </div>

        <!-- Main column -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden relative">
            <AppHeader @toggle-sidebar="toggleSidebar" :is-sidebar-collapsed="isSidebarCollapsed" />
            <main class="flex-1 overflow-y-auto p-3 sm:p-4 lg:p-6">
                <slot />
            </main>
            <AppFooter />
        </div>

    </div>

    <!-- Global toast stack + flash watcher (outside layout flow) -->
    <AppToast />
    <AppNotifications />
</template>
