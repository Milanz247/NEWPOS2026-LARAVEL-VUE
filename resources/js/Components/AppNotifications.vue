<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast.js';

const page    = usePage();
const { success, error, warning, info } = useToast();

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) success(flash.success);
        if (flash?.error)   error(flash.error);
        if (flash?.warning) warning(flash.warning);
        if (flash?.info)    info(flash.info);
    },
    { deep: true, immediate: true },
);
</script>

<template><span /></template>
