<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Autentikasi Portal') - {{ config('app.name', 'Sarunis') }}</title>
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
    <body class="portal-auth-body">
        <div class="portal-auth-shell d-flex align-items-center">
            <div class="container-xxl">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <section class="card border-0 portal-auth-card" aria-label="Halaman autentikasi portal">
                            <div class="row g-0">
                                <div class="col-12 col-lg-5 portal-auth-aside">
                                    <div class="portal-auth-orb">
                                        @include('auth.portal.partials.asset', [
                                            'asset' => $portalView['asset'],
                                            'portalLabel' => $portalView['display'],
                                        ])
                                    </div>
                                </div>

                                <div class="col-12 col-lg-7 portal-auth-form-column">
                                    <div class="portal-auth-notch portal-auth-notch--top" aria-hidden="true"></div>
                                    <div class="portal-auth-notch portal-auth-notch--bottom" aria-hidden="true"></div>

                                    <div class="card-body h-100 d-flex align-items-center justify-content-center p-3 p-sm-4 p-lg-5">
                                        <div class="portal-auth-form-wrap w-100">
                                            @yield('content')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous"
        ></script>

        @stack('scripts')
    </body>
</html>
