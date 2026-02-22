import Swal from 'sweetalert2';
import { toast } from 'vue-sonner';

// ─────────────────────────────────────────────
//  SWEETALERT2  —  Configured Instance
// ─────────────────────────────────────────────
const swalBase = Swal.mixin({
    customClass: {
        popup:             'swal-popup',
        title:             'swal-title',
        htmlContainer:     'swal-html',
        confirmButton:     'swal-btn-confirm',
        cancelButton:      'swal-btn-cancel',
        icon:              'swal-icon',
        actions:           'swal-actions',
    },
    buttonsStyling:   false,
    reverseButtons:   true,
    focusConfirm:     false,
    showClass: {
        popup: 'swal-show',
    },
    hideClass: {
        popup: 'swal-hide',
    },
});

// ─────────────────────────────────────────────
//  PUBLIC API
// ─────────────────────────────────────────────

/**
 * Show a success toast
 */
export const toastSuccess = (message, options = {}) => {
    toast.success(message, {
        duration: options.duration ?? 4000,
        description: options.description ?? undefined,
    });
};

/**
 * Show an error toast
 */
export const toastError = (message, options = {}) => {
    toast.error(message, {
        duration: options.duration ?? 5000,
        description: options.description ?? undefined,
    });
};

/**
 * Show an info / neutral toast
 */
export const toastInfo = (message, options = {}) => {
    toast(message, {
        duration: options.duration ?? 4000,
        description: options.description ?? undefined,
    });
};

/**
 * Show a delete confirmation dialog.
 * Returns true if confirmed.
 */
export const confirmDelete = async (options = {}) => {
    const result = await swalBase.fire({
        title:              options.title   ?? 'Are you sure?',
        text:               options.text    ?? 'This action cannot be undone.',
        icon:               'warning',
        showCancelButton:   true,
        confirmButtonText:  options.confirm ?? 'Yes, Delete',
        cancelButtonText:   options.cancel  ?? 'Cancel',
    });
    return result.isConfirmed;
};

/**
 * Generic confirmation dialog.
 * Returns true if confirmed.
 */
export const confirm = async (options = {}) => {
    const result = await swalBase.fire({
        title:              options.title   ?? 'Confirm Action',
        text:               options.text    ?? 'Do you want to proceed?',
        icon:               options.icon    ?? 'question',
        showCancelButton:   true,
        confirmButtonText:  options.confirm ?? 'Confirm',
        cancelButtonText:   options.cancel  ?? 'Cancel',
    });
    return result.isConfirmed;
};

// ─────────────────────────────────────────────
//  VUE PLUGIN  —  app.use(AlertPlugin)
// ─────────────────────────────────────────────
export const AlertPlugin = {
    install(app) {
        app.config.globalProperties.$toast   = { success: toastSuccess, error: toastError, info: toastInfo };
        app.config.globalProperties.$confirm = confirm;
        app.config.globalProperties.$confirmDelete = confirmDelete;
    },
};

export default AlertPlugin;
