<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page       = usePage();
const appVersion = computed(() => page.props.appVersion ?? 'v1.0.0');
const year       = new Date().getFullYear();
</script>

<template>
    <!-- ══════════════════════════════════════
         FOOTER
    ══════════════════════════════════════ -->
    <footer class="border-t border-brand-border bg-white px-3 sm:px-6 py-2 sm:py-3 flex flex-col sm:flex-row items-center justify-between gap-1.5 sm:gap-2">
        <div class="flex items-center gap-3 text-xs text-brand-muted">
            <span class="badge badge-default py-0.5">{{ appVersion }}</span>
            <span>© {{ year }} All rights reserved.</span>
        </div>
        <div class="text-xs text-brand-muted">
            Developed by
            <a href="https://milanmadusanka.me/" target="_blank" rel="noopener noreferrer"
               class="font-semibold text-brand-dark hover:underline underline-offset-2 transition-colors">
                Milan Madusanka
            </a>
        </div>
    </footer>
</template>
