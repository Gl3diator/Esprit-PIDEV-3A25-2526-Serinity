(function () {
    function mapLegacyDataApi(root) {
        root.querySelectorAll('[data-toggle]').forEach(function (el) {
            if (!el.hasAttribute('data-bs-toggle')) {
                el.setAttribute('data-bs-toggle', el.getAttribute('data-toggle'));
            }
        });

        root.querySelectorAll('[data-target]').forEach(function (el) {
            if (!el.hasAttribute('data-bs-target')) {
                el.setAttribute('data-bs-target', el.getAttribute('data-target'));
            }
        });

        root.querySelectorAll('[data-dismiss]').forEach(function (el) {
            if (!el.hasAttribute('data-bs-dismiss')) {
                el.setAttribute('data-bs-dismiss', el.getAttribute('data-dismiss'));
            }
        });

        root.querySelectorAll('[data-placement]').forEach(function (el) {
            if (!el.hasAttribute('data-bs-placement')) {
                el.setAttribute('data-bs-placement', el.getAttribute('data-placement'));
            }
        });

        root.querySelectorAll('[data-trigger]').forEach(function (el) {
            if (!el.hasAttribute('data-bs-trigger')) {
                el.setAttribute('data-bs-trigger', el.getAttribute('data-trigger'));
            }
        });
    }

    function initTooltips(root) {
        if (!window.bootstrap || !window.bootstrap.Tooltip) {
            return;
        }

        root.querySelectorAll('[data-bs-toggle="tooltip"], [data-toggle="tooltip"], [rel="tooltip"]').forEach(function (el) {
            window.bootstrap.Tooltip.getOrCreateInstance(el);
        });
    }

    function initPopovers(root) {
        if (!window.bootstrap || !window.bootstrap.Popover) {
            return;
        }

        root.querySelectorAll('[data-bs-toggle="popover"], [data-toggle="popover"]').forEach(function (el) {
            window.bootstrap.Popover.getOrCreateInstance(el);
        });
    }

    function initInputGroupFocus(root) {
        root.querySelectorAll('.form-control').forEach(function (input) {
            if (input.dataset.argonFocusBound === '1') {
                return;
            }

            input.dataset.argonFocusBound = '1';

            input.addEventListener('focus', function () {
                var group = input.closest('.input-group');
                if (group) {
                    group.classList.add('input-group-focus');
                }
            });

            input.addEventListener('blur', function () {
                var group = input.closest('.input-group');
                if (group) {
                    group.classList.remove('input-group-focus');
                }
            });
        });
    }

    function initNavbarCollapseTransition(root) {
        root.querySelectorAll('.navbar .collapse').forEach(function (collapseEl) {
            if (collapseEl.dataset.argonCollapseBound === '1') {
                return;
            }

            collapseEl.dataset.argonCollapseBound = '1';

            collapseEl.addEventListener('hide.bs.collapse', function () {
                collapseEl.classList.add('collapsing-out');
            });

            collapseEl.addEventListener('hidden.bs.collapse', function () {
                collapseEl.classList.remove('collapsing-out');
            });
        });
    }

    function initArgonBootstrap5Bridge(root) {
        mapLegacyDataApi(root);
        initTooltips(root);
        initPopovers(root);
        initInputGroupFocus(root);
        initNavbarCollapseTransition(root);
    }

    function run() {
        initArgonBootstrap5Bridge(document);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }

    document.addEventListener('turbo:load', run);
})();
