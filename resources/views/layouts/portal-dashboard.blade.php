<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Dashboard Portal') - {{ config('app.name', 'Sarunis') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous"
        >
        <style>
            {!! file_get_contents(resource_path('css/app.css')) !!}
        </style>
    </head>
    <body class="portal-dashboard-body">
        @yield('content')

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const body = document.body;
                const openButton = document.querySelector('[data-sidebar-open]');
                const closeButtons = document.querySelectorAll('[data-sidebar-close]');
                const collapseButton = document.querySelector('[data-sidebar-collapse]');
                const collapsedKey = 'sarunis.sidebar.collapsed';

                if (localStorage.getItem(collapsedKey) === '1') {
                    body.classList.add('portal-sidebar-collapsed');
                    collapseButton?.setAttribute('aria-pressed', 'true');
                    collapseButton?.setAttribute('aria-label', 'Perbesar sidebar');
                }

                const setMobileDrawer = function (open) {
                    body.classList.toggle('portal-sidebar-open', open);
                    openButton?.setAttribute('aria-expanded', open ? 'true' : 'false');
                };

                openButton?.addEventListener('click', function () {
                    setMobileDrawer(true);
                });

                closeButtons.forEach(function (button) {
                    button.addEventListener('click', function () {
                        setMobileDrawer(false);
                    });
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        setMobileDrawer(false);
                    }
                });

                collapseButton?.addEventListener('click', function () {
                    const collapsed = !body.classList.contains('portal-sidebar-collapsed');
                    body.classList.toggle('portal-sidebar-collapsed', collapsed);
                    collapseButton.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
                    collapseButton.setAttribute('aria-label', collapsed ? 'Perbesar sidebar' : 'Perkecil sidebar');
                    localStorage.setItem(collapsedKey, collapsed ? '1' : '0');
                });

                const labelTables = function (root) {
                    const tables = new Set();

                    if (root.matches?.('.portal-directory-table, .portal-table, table')) {
                        tables.add(root);
                    }

                    root.querySelectorAll?.('.portal-directory-table, .portal-table, table').forEach(function (table) {
                        tables.add(table);
                    });

                    root.closest?.('table') && tables.add(root.closest('table'));

                    tables.forEach(function (table) {
                        const headers = Array.from(table.querySelectorAll('thead th')).map(function (th) {
                            return th.textContent.trim();
                        });

                        if (headers.length === 0) return;

                        table.querySelectorAll('tbody tr').forEach(function (row) {
                            Array.from(row.children).forEach(function (cell, index) {
                                if (!cell.hasAttribute('data-label') && headers[index]) {
                                    cell.setAttribute('data-label', headers[index]);
                                }
                            });
                        });
                    });
                };

                const enhanceButtons = function (root) {
                    root.querySelectorAll('[aria-label]').forEach(function (element) {
                        if (!element.getAttribute('title')) {
                            element.setAttribute('title', element.getAttribute('aria-label'));
                        }
                    });

                    if (window.bootstrap?.Tooltip) {
                        root.querySelectorAll('[title]').forEach(function (element) {
                            if (element.dataset.tooltipBound === '1') return;
                            element.dataset.tooltipBound = '1';
                            new window.bootstrap.Tooltip(element);
                        });
                    }
                };

                labelTables(document);
                enhanceButtons(document);

                const observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        mutation.addedNodes.forEach(function (node) {
                            if (node instanceof HTMLElement) {
                                labelTables(node);
                                enhanceButtons(node);
                            }
                        });
                    });
                });

                observer.observe(document.body, { childList: true, subtree: true });
            });
        </script>
        @stack('scripts')
    </body>
</html>
