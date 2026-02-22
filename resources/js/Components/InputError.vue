<script setup>
defineProps({
    message: { type: String },
});
</script>

<template>
    <div v-show="message">
        <p class="mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="w-3.5 h-3.5 fill-red-500 shrink-0"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1Zm-.75 4a.75.75 0 0 1 1.5 0v3.25a.75.75 0 0 1-1.5 0V5Zm.75 6.5a.875.875 0 1 1 0-1.75.875.875 0 0 1 0 1.75Z"/></svg>
            {{ message }}
        </p>
    </div>
</template>
