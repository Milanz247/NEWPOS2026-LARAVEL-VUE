import { reactive } from 'vue';

/**
 * Module-level state — shared across the entire app.
 * Any component can import { addToast } and push a toast.
 */
const toasts = reactive([]);
let nextId = 1;

const DURATIONS = { success: 4000, error: 5000, warning: 4500, info: 4000 };

export function addToast(type, message, options = {}) {
    const id       = nextId++;
    const duration = options.duration ?? DURATIONS[type] ?? 4000;

    toasts.push({ id, type, message, description: options.description ?? null, duration, progress: 100 });

    // Progress bar countdown
    const interval = 30;
    const steps    = duration / interval;
    const decrement = 100 / steps;
    let timer = null;

    const tick = () => {
        const toast = toasts.find(t => t.id === id);
        if (!toast) { clearInterval(timer); return; }
        toast.progress -= decrement;
        if (toast.progress <= 0) {
            clearInterval(timer);
            removeToast(id);
        }
    };
    timer = setInterval(tick, interval);

    return id;
}

export function removeToast(id) {
    const idx = toasts.findIndex(t => t.id === id);
    if (idx !== -1) toasts.splice(idx, 1);
}

export function useToast() {
    return {
        toasts,
        addToast,
        removeToast,
        success: (msg, opts) => addToast('success', msg, opts),
        error:   (msg, opts) => addToast('error',   msg, opts),
        warning: (msg, opts) => addToast('warning', msg, opts),
        info:    (msg, opts) => addToast('info',    msg, opts),
    };
}
