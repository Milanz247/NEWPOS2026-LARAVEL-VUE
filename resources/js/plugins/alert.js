import Swal from 'sweetalert2';
import { useToast } from '@/composables/useToast.js';

const { success: _success, error: _error, warning: _warning, info: _info } = useToast();

// ─────────────────────────────────────────────
//  TOAST  —  Thin wrappers around useToast
// ─────────────────────────────────────────────
export const toastSuccess = (message, options = {}) => _success(message, options);
export const toastError   = (message, options = {}) => _error(message, options);
export const toastWarning = (message, options = {}) => _warning(message, options);
export const toastInfo    = (message, options = {}) => _info(message, options);

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
        app.config.globalProperties.$toast = {
            success: toastSuccess,
            error:   toastError,
            warning: toastWarning,
            info:    toastInfo,
        };
        app.config.globalProperties.$confirm = confirm;
        app.config.globalProperties.$confirmDelete = confirmDelete;
    },
};

export default AlertPlugin;
