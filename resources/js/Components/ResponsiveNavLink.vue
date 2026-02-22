<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href:   { type: String, required: true },
    active: { type: Boolean, default: false },
});
</script>

<template>
    <Link
        :href="href"
        :class="[
            'flex w-full items-center gap-2.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-150',
            active
                ? 'bg-brand-light text-brand-dark font-semibold'
                : 'text-brand-accent hover:bg-brand-light hover:text-brand-dark'
        ]"
    >
        <slot />
    </Link>
</template>
