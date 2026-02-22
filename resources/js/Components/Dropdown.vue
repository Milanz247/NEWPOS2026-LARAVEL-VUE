<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: { type: String, default: 'right' },
    width: { type: String, default: '56' },
});

const open = ref(false);

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') open.value = false;
};

onMounted(()  => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const alignmentClasses = computed(() => {
    if (props.align === 'left')  return 'origin-top-left start-0';
    if (props.align === 'right') return 'origin-top-right end-0';
    return 'origin-top';
});
</script>

<template>
    <div class="relative">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <!-- Backdrop -->
        <div v-show="open" class="fixed inset-0 z-40" @click="open = false" />

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95 translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-1"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-2 w-56 card p-1"
                :class="alignmentClasses"
                @click="open = false"
            >
                <slot name="content" />
            </div>
        </Transition>
    </div>
</template>
