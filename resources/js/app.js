import './bootstrap';

document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-modal-open]');
    if (opener) document.getElementById(opener.dataset.modalOpen)?.showModal();

    const closer = event.target.closest('[data-modal-close]');
    if (closer) document.getElementById(closer.dataset.modalClose)?.close();

    const toastOpener = event.target.closest('[data-toast-open]');
    if (toastOpener) {
        const toast = document.getElementById(toastOpener.dataset.toastOpen);
        if (toast) {
            toast.classList.remove('hidden');
            toast.classList.add('flex');
            window.setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('flex');
            }, 4000);
        }
    }

    const toastCloser = event.target.closest('[data-toast-close]');
    if (toastCloser) {
        const toast = toastCloser.closest('[data-toast]');
        toast?.classList.add('hidden');
        toast?.classList.remove('flex');
    }

    const dismiss = event.target.closest('[data-dismiss]');
    if (dismiss) dismiss.closest('[role="alert"]')?.remove();

    const trigger = event.target.closest('[data-tab-trigger]');
    if (trigger) activateTab(trigger);
});

function activateTab(trigger) {
    const tabset = trigger.closest('[data-tabs]');
    if (!tabset) return;

    tabset.querySelectorAll('[data-tab-trigger]').forEach((tab) => {
        const active = tab === trigger;
        tab.setAttribute('aria-selected', String(active));
        tab.tabIndex = active ? 0 : -1;
        tab.classList.toggle('border-secondary', active);
        tab.classList.toggle('text-secondary', active);
        tab.classList.toggle('border-transparent', !active);
        tab.classList.toggle('text-slate-500', !active);
        const panel = document.getElementById(tab.getAttribute('aria-controls'));
        if (panel) panel.hidden = !active;
    });
}

document.addEventListener('keydown', (event) => {
    const tab = event.target.closest?.('[data-tab-trigger]');
    if (tab && ['ArrowRight', 'ArrowLeft', 'Home', 'End'].includes(event.key)) {
        event.preventDefault();
        const tabs = [...tab.closest('[data-tabs]').querySelectorAll('[data-tab-trigger]')];
        const index = tabs.indexOf(tab);
        const next = event.key === 'Home' ? 0 : event.key === 'End' ? tabs.length - 1 : (index + (event.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length;
        tabs[next].focus();
        activateTab(tabs[next]);
    }

    if (event.key === 'Escape') document.querySelectorAll('details[open]').forEach((menu) => menu.removeAttribute('open'));
});

document.addEventListener('click', (event) => {
    document.querySelectorAll('details[open]').forEach((menu) => {
        if (!menu.contains(event.target)) menu.removeAttribute('open');
    });
});
