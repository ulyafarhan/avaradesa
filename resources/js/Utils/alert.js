export const alert = {
    success(title, text = '') {
        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: title } }));
    },

    error(title, text = '') {
        window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: title } }));
    },

    confirm(title, text = '', confirmText = 'Ya', cancelText = 'Batal') {
        const isConfirmed = window.confirm(`${title}\n${text}`);
        return Promise.resolve({ isConfirmed });
    },

    toast(title, icon = 'success') {
        window.dispatchEvent(new CustomEvent('toast', { detail: { type: icon, message: title } }));
    }
};
