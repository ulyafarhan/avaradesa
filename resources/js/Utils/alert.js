import Swal from 'sweetalert2';

/**
 * Utilitas pembungkus SweetAlert2 dengan gaya kustom.
 * Menyelaraskan tampilan dialog modal/toast dengan tema desain premium (Teal & Slate).
 */
const customSwal = Swal.mixin({
    customClass: {
        confirmButton: 'px-6 py-3 rounded-full bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-500/50 cursor-pointer mx-1',
        cancelButton: 'px-6 py-3 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-350 cursor-pointer mx-1',
        popup: 'rounded-3xl border border-slate-200 bg-white shadow-2xl p-6',
        title: 'text-lg font-bold text-slate-900 tracking-tight leading-tight',
        htmlContainer: 'text-xs text-slate-600 font-medium leading-relaxed mt-2'
    },
    buttonsStyling: false
});

/**
 * Kumpulan fungsi helper SweetAlert2 untuk aksi responsif.
 */
export const alert = {
    success(title, text = '') {
        return customSwal.fire({
            icon: 'success',
            title,
            text,
            iconColor: '#0d9488', // teal-600
        });
    },

    error(title, text = '') {
        return customSwal.fire({
            icon: 'error',
            title,
            text,
            iconColor: '#e11d48', // rose-600
        });
    },

    confirm(title, text = '', confirmText = 'Ya', cancelText = 'Batal') {
        return customSwal.fire({
            title,
            text,
            icon: 'warning',
            iconColor: '#eab308', // amber-500
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true
        });
    },

    toast(title, icon = 'success') {
        const toastSwal = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            iconColor: icon === 'success' ? '#0d9488' : '#e11d48',
            customClass: {
                popup: 'rounded-2xl border border-slate-200 bg-white/95 backdrop-blur-sm shadow-xl p-3 text-xs font-bold text-slate-800',
            }
        });
        return toastSwal.fire({
            icon,
            title
        });
    }
};
