import './bootstrap';

document.getElementById('attachments')?.addEventListener('change', (event) => {
    const fileList = document.getElementById('file-list');
    if (!fileList) return;
    fileList.replaceChildren();
    Array.from(event.target.files ?? []).forEach((file) => {
        const item = document.createElement('li');
        item.textContent = `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB`;
        fileList.append(item);
    });
});

document.getElementById('file-input')?.addEventListener('change', (event) => {
    const file = event.target.files?.[0];
    const wrapper = document.getElementById('preview-wrapper');
    const preview = document.getElementById('preview');
    if (!wrapper || !preview) return;
    if (!file) {
        if (preview.dataset.objectUrl) URL.revokeObjectURL(preview.dataset.objectUrl);
        delete preview.dataset.objectUrl;
        wrapper.classList.add('hidden');
        preview.removeAttribute('src');
        return;
    }
    if (preview.dataset.objectUrl) URL.revokeObjectURL(preview.dataset.objectUrl);
    const objectUrl = URL.createObjectURL(file);
    preview.dataset.objectUrl = objectUrl;
    preview.src = objectUrl;
    wrapper.classList.remove('hidden');
});

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

    const passwordToggle = event.target.closest('[data-password-toggle]');
    if (passwordToggle) {
        const input = document.getElementById(passwordToggle.dataset.passwordToggle);
        if (input) {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', String(show));
            passwordToggle.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
        }
    }

    const sidebarToggle = event.target.closest('[data-admin-sidebar-toggle]');
    if (sidebarToggle) toggleAdminSidebar();

    const heroControl = event.target.closest('[data-hero-prev], [data-hero-next], [data-hero-dot]');
    if (heroControl) moveHeroCarousel(heroControl);

    if (event.target.closest('[data-admin-sidebar-backdrop]')) closeMobileAdminSidebar();
});

function moveHeroCarousel(control) {
    const carousel = control.closest('[data-hero-carousel]');
    if (!carousel) return;

    const slides = [...carousel.querySelectorAll('[data-hero-slide]')];
    const dots = [...carousel.querySelectorAll('[data-hero-dot]')];
    if (slides.length < 2) return;

    const current = slides.findIndex((slide) => !slide.classList.contains('hidden'));
    const requested = control.hasAttribute('data-hero-dot')
        ? Number(control.dataset.heroDot)
        : current + (control.hasAttribute('data-hero-next') ? 1 : -1);
    const next = (requested + slides.length) % slides.length;

    slides.forEach((slide, index) => {
        const active = index === next;
        slide.classList.toggle('hidden', !active);
        slide.classList.toggle('flex', active);
        slide.setAttribute('aria-hidden', String(!active));
    });
    dots.forEach((dot, index) => dot.setAttribute('aria-current', String(index === next)));
}

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

function toggleAdminSidebar() {
    const sidebar = document.querySelector('[data-admin-sidebar]');
    const content = document.querySelector('[data-admin-content]');
    const backdrop = document.querySelector('[data-admin-sidebar-backdrop]');
    if (!sidebar || !content) return;

    if (window.matchMedia('(min-width: 1024px)').matches) {
        const collapsed = sidebar.dataset.collapsed !== 'true';
        sidebar.dataset.collapsed = String(collapsed);
        sidebar.classList.toggle('lg:w-20', collapsed);
        sidebar.classList.toggle('lg:w-72', !collapsed);
        content.classList.toggle('lg:ml-20', collapsed);
        content.classList.toggle('lg:ml-72', !collapsed);
        document.querySelectorAll('[data-sidebar-label]').forEach((label) => label.classList.toggle('hidden', collapsed));
        document.querySelectorAll('[data-admin-sidebar-toggle]').forEach((button) => {
            button.setAttribute('aria-expanded', String(!collapsed));
            button.setAttribute('aria-label', collapsed ? 'Expandir navegación' : 'Contraer navegación');
        });
        document.querySelector('[data-sidebar-chevron]')?.classList.toggle('rotate-180', collapsed);
        return;
    }

    const open = sidebar.dataset.mobileOpen !== 'true';
    sidebar.dataset.mobileOpen = String(open);
    sidebar.classList.toggle('-translate-x-full', !open);
    sidebar.classList.toggle('translate-x-0', open);
    backdrop?.classList.toggle('hidden', !open);
    document.querySelectorAll('[data-admin-sidebar-toggle]').forEach((button) => button.setAttribute('aria-expanded', String(open)));
}

function closeMobileAdminSidebar() {
    const sidebar = document.querySelector('[data-admin-sidebar]');
    const backdrop = document.querySelector('[data-admin-sidebar-backdrop]');
    if (!sidebar) return;
    sidebar.dataset.mobileOpen = 'false';
    sidebar.classList.add('-translate-x-full');
    sidebar.classList.remove('translate-x-0');
    backdrop?.classList.add('hidden');
    document.querySelectorAll('[data-admin-sidebar-toggle]').forEach((button) => button.setAttribute('aria-expanded', 'false'));
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeMobileAdminSidebar();
});
