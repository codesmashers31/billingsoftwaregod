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
 * Confirm Action Helper
 */
function confirmAction(message, onConfirm) {
    if (confirm(message)) {
        onConfirm();
    }
}
