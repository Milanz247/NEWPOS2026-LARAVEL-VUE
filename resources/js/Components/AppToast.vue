<script setup>
import { useToast, removeToast } from '@/composables/useToast.js';

const { toasts } = useToast();

const icons = {
    success: `<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>`,
    error:   `<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/></svg>`,
    warning: `<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>`,
    info:    `<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/></svg>`,
};
</script>

<template>
    <!-- Portal: fixed top-right, zero impact on layout -->
    <Teleport to="body">
        <div class="toast-stack" aria-live="polite" aria-label="Notifications">
            <TransitionGroup name="toast" tag="div" class="toast-inner">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    :class="['toast-item', `toast-${toast.type}`]"
                    role="alert"
                >
                    <!-- Left accent + icon -->
                    <div class="toast-icon" v-html="icons[toast.type] ?? icons.info" />

                    <!-- Text -->
                    <div class="toast-body">
                        <p class="toast-title">{{ toast.message }}</p>
                        <p v-if="toast.description" class="toast-desc">{{ toast.description }}</p>
                    </div>

                    <!-- Close button -->
                    <button class="toast-close" @click="removeToast(toast.id)" aria-label="Dismiss">
                        <svg viewBox="0 0 16 16" fill="currentColor" width="12" height="12">
                            <path d="M3.72 3.72a.75.75 0 011.06 0L8 6.94l3.22-3.22a.75.75 0 111.06 1.06L9.06 8l3.22 3.22a.75.75 0 11-1.06 1.06L8 9.06l-3.22 3.22a.75.75 0 01-1.06-1.06L6.94 8 3.72 4.78a.75.75 0 010-1.06z"/>
                        </svg>
                    </button>

                    <!-- Progress bar -->
                    <div class="toast-progress">
                        <div class="toast-progress-bar" :style="{ width: toast.progress + '%' }" />
                    </div>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
