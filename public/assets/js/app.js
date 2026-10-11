/**
 * Divya Murti ERP - Main Core Application JS
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Live Clock
    const clockEl = document.getElementById('header-live-clock');
    if (clockEl) {
        const updateClock = () => {
            const now = new Date();
            clockEl.textContent = now.toLocaleDateString('en-IN', {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }) + ' ' + now.toLocaleTimeString('en-IN', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        };
        updateClock();
        setInterval(updateClock, 1000);
    }

    // 2. Global AJAX Form Submitter with Data-Action
    document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const origText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            }

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showToast('success', result.message || 'Operation successful!');
                    if (result.data && result.data.redirect) {
                        setTimeout(() => {
                            window.location.href = result.data.redirect;
                        }, 800);
                    } else if (form.dataset.reload === 'true') {
                        setTimeout(() => window.location.reload(), 800);
                    } else if (form.dataset.modal) {
                        closeModal(form.dataset.modal);
                    }
                } else {
                    let msg = result.message || 'An error occurred.';
                    if (result.errors) {
                        const errorList = Object.values(result.errors).flat().join('<br>');
                        msg += '<br><span class="text-xs">' + errorList + '</span>';
                    }
                    showToast('error', msg);
                }
            } catch (err) {
                console.error(err);
                showToast('error', 'Network communication error. Please try again.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = origText;
                }
            }
        });
    });
});

/**
 * Toast Notification System
 */
function showToast(type = 'success', message = '') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-5 right-5 z-50 flex flex-col space-y-3 pointer-events-none';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'pointer-events-auto flex items-center p-4 min-w-[320px] max-w-md rounded-xl shadow-2xl transition-all duration-300 transform translate-y-4 opacity-0 border';

    let icon = '';
    let bgClasses = '';

    if (type === 'success') {
        bgClasses = 'bg-slate-900 text-amber-300 border-amber-500/40';
        icon = '<div class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center mr-3 shrink-0"><i class="fas fa-check"></i></div>';
    } else if (type === 'error') {
        bgClasses = 'bg-slate-900 text-rose-300 border-rose-500/40';
        icon = '<div class="w-8 h-8 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center mr-3 shrink-0"><i class="fas fa-exclamation-triangle"></i></div>';
    } else {
        bgClasses = 'bg-slate-900 text-blue-300 border-blue-500/40';
        icon = '<div class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center mr-3 shrink-0"><i class="fas fa-info-circle"></i></div>';
    }

    toast.className += ` ${bgClasses}`;
    toast.innerHTML = `
        ${icon}
        <div class="flex-1 text-sm font-medium pr-2">${message}</div>
        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white shrink-0 p-1">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    }, 10);

    // Auto dismiss after 4 seconds
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

/**
 * Modal Controllers
 */
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
}

/**
 * Interactive Confirmation Dialog (Replaces native browser confirm)
 */
function showConfirmDialog(options = {}) {
    return new Promise((resolve) => {
        let dialog = document.getElementById('app-confirm-modal');
        if (!dialog) {
            dialog = document.createElement('div');
            dialog.id = 'app-confirm-modal';
            dialog.className = 'fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-200';
            dialog.innerHTML = `
                <div id="app-confirm-box" class="bg-white border border-slate-200/80 rounded-2xl w-full max-w-sm sm:max-w-md shadow-2xl p-6 relative transform scale-95 opacity-0 transition-all duration-200 text-center animate-fade-in">
                    <div id="app-confirm-icon-wrap" class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm">
                        <i id="app-confirm-icon" class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 id="app-confirm-title" class="text-lg font-extrabold text-slate-900 tracking-tight mb-2">Are you sure?</h3>
                    <div id="app-confirm-message" class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-6 font-medium">This action cannot be undone.</div>
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" id="app-confirm-cancel-btn" class="w-1/2 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs sm:text-sm font-bold transition-all shadow-sm">
                            Cancel
                        </button>
                        <button type="button" id="app-confirm-ok-btn" class="w-1/2 py-2.5 rounded-xl text-white text-xs sm:text-sm font-bold shadow-md transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                            Confirm
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(dialog);
        }

        const titleEl = document.getElementById('app-confirm-title');
        const msgEl = document.getElementById('app-confirm-message');
        const iconWrap = document.getElementById('app-confirm-icon-wrap');
        const iconEl = document.getElementById('app-confirm-icon');
        const okBtn = document.getElementById('app-confirm-ok-btn');
        const cancelBtn = document.getElementById('app-confirm-cancel-btn');
        const box = document.getElementById('app-confirm-box');

        const title = options.title || 'Delete Confirmation';
        const message = typeof options === 'string' ? options : (options.message || 'Are you sure you want to proceed?');
        const confirmText = options.confirmText || 'Yes, Delete';
        const cancelText = options.cancelText || 'Cancel';
        const type = options.type || 'danger';

        titleEl.textContent = title;
        msgEl.innerHTML = message;
        okBtn.innerHTML = `<i class="fas fa-trash-alt mr-1.5"></i> ${confirmText}`;
        cancelBtn.textContent = cancelText;

        if (type === 'danger') {
            iconWrap.className = 'w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-500 flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm';
            iconEl.className = 'fas fa-trash-alt text-rose-500';
            okBtn.className = 'w-1/2 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-rose-500/25 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5';
        } else if (type === 'warning') {
            iconWrap.className = 'w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm';
            iconEl.className = 'fas fa-exclamation-triangle text-amber-500';
            okBtn.className = 'w-1/2 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-amber-500/25 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5';
        } else {
            iconWrap.className = 'w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-500 flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm';
            iconEl.className = 'fas fa-info-circle text-indigo-500';
            okBtn.className = 'w-1/2 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white text-xs sm:text-sm font-bold shadow-md hover:shadow-indigo-500/25 transition-all flex items-center justify-center gap-2 transform hover:-translate-y-0.5';
        }

        dialog.classList.remove('hidden');
        dialog.classList.add('flex');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);

        function cleanup(result) {
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                dialog.classList.remove('flex');
                dialog.classList.add('hidden');
                resolve(result);
            }, 180);
            document.removeEventListener('keydown', handleKey);
        }

        function handleKey(e) {
            if (e.key === 'Escape') cleanup(false);
        }

        okBtn.onclick = () => cleanup(true);
        cancelBtn.onclick = () => cleanup(false);
        dialog.onclick = (e) => {
            if (e.target === dialog) cleanup(false);
        };
        document.addEventListener('keydown', handleKey);
    });
}
window.showConfirmDialog = showConfirmDialog;

/**
 * Confirm Action Helper
 */
async function confirmAction(message, onConfirm) {
    const ok = await showConfirmDialog({
        title: 'Confirmation',
        message: message,
        confirmText: 'Confirm',
        type: 'danger'
    });
    if (ok && typeof onConfirm === 'function') {
        onConfirm();
    }
}
window.confirmAction = confirmAction;
