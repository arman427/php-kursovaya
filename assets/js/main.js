document.addEventListener('DOMContentLoaded', function () {
    const modal = document.querySelector('#auth-modal');
    const form = modal ? modal.querySelector('.auth-form') : null;
    const title = modal ? modal.querySelector('#auth-title') : null;
    const action = form ? form.querySelector('[name="auth_action"]') : null;
    const confirmation = form ? form.querySelector('.register-only') : null;

    function setAuthMode(mode) {
        const isRegister = mode === 'register';
        if (title) title.textContent = isRegister ? 'Регистрация' : 'Войти в аккаунт';
        if (action) action.value = mode;
        if (confirmation) confirmation.classList.toggle('is-visible', isRegister);
        document.querySelectorAll('[data-auth-tab]').forEach(function (tab) {
            tab.classList.toggle('is-active', tab.dataset.authTab === mode);
        });
    }

    function openModal() {
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        const firstInput = modal.querySelector('input[type="email"]');
        if (firstInput) firstInput.focus();
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    const profileMenus = document.querySelectorAll('[data-profile-menu]');

    function setProfileMenuState(menu, isOpen) {
        const toggle = menu.querySelector('[data-profile-toggle]');
        const dropdown = menu.querySelector('[data-profile-dropdown]');
        menu.classList.toggle('is-open', isOpen);
        if (toggle) toggle.setAttribute('aria-expanded', String(isOpen));
        if (dropdown) dropdown.setAttribute('aria-hidden', String(!isOpen));
    }

    profileMenus.forEach(function (menu) {
        const toggle = menu.querySelector('[data-profile-toggle]');
        if (!toggle) return;

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const shouldOpen = !menu.classList.contains('is-open');
            profileMenus.forEach(function (otherMenu) {
                setProfileMenuState(otherMenu, false);
            });
            setProfileMenuState(menu, shouldOpen);
        });
    });

    document.addEventListener('click', function (event) {
        profileMenus.forEach(function (menu) {
            if (!menu.contains(event.target)) setProfileMenuState(menu, false);
        });
    });

    document.querySelectorAll('[data-auth-open]').forEach(function (button) { button.addEventListener('click', openModal); });
    document.querySelectorAll('[data-auth-close]').forEach(function (button) { button.addEventListener('click', closeModal); });
    document.querySelectorAll('[data-auth-tab]').forEach(function (tab) { tab.addEventListener('click', function () { setAuthMode(tab.dataset.authTab); }); });
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        closeModal();
        profileMenus.forEach(function (menu) { setProfileMenuState(menu, false); });
    });

    if (document.querySelector('.form-error')) openModal();
});
