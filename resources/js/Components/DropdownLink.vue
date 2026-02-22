<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href:   { type: String, required: true },
    method: { type: String, default: 'get' },
    as:     { type: String, default: 'a' },
});
</script>

<template>
    <Link
        :href="href"
        :method="method"
        :as="as"
        class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-brand-accent rounded-lg hover:bg-brand-light hover:text-brand-dark transition-colors duration-150"
    >
        <slot />
    </Link>
</template>
