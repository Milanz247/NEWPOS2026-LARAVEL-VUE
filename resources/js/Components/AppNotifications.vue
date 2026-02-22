<script setup>
import { onMounted, onUnmounted } from 'vue';
import { usePage, router }        from '@inertiajs/vue3';
import { Toaster }                 from 'vue-sonner';
import { toastSuccess, toastError } from '@/plugins/alert.js';

const page = usePage();

const handleFlash = () => {
    const flash = page.props.flash;
    if (flash?.success) toastSuccess(flash.success);
    if (flash?.error)   toastError(flash.error);
};

// On initial mount (page load / hard refresh)
onMounted(() => handleFlash());

// Listen to Inertia navigation finish events — reliable for flash detection
const removeFinishListener = router.on('finish', () => {
    // Small delay to ensure props are updated
    setTimeout(handleFlash, 50);
});

onUnmounted(() => {
    if (removeFinishListener) removeFinishListener();
});
</script>

<template>
    <!--
        Toaster component from vue-sonner.
        Positioned top-right with our custom theme class.
    -->
    <Toaster
        position="top-right"
        :expand="true"
        :duration="5000"
        :visibleToasts="5"
        :toastOptions="{
            unstyled: true,
            classNames: {
                toast:       'sn-toast',
                title:       'sn-title',
                description: 'sn-desc',
                success:     'sn-success',
                error:       'sn-error',
                icon:        'sn-icon',
            },
        }"
        :containerStyle="{
            zIndex: 999999,
            padding: '20px',
        }"
    />
</template>
