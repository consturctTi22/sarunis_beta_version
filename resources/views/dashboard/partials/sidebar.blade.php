@php
    $roleLabels = [
        'admin' => 'Admin',
        'guru_mapel' => 'Guru Mapel',
        'walikelas' => 'Wali Kelas',
        'siswa' => 'Siswa',
    ];
    $userRoles = collect(auth()->user()?->roles ?? [])
        ->map(fn (string $role): string => $roleLabels[$role] ?? str_replace('_', ' ', $role))
        ->values();
@endphp

<aside class="portal-dashboard-sidebar" aria-label="Navigasi dashboard">
    <div class="portal-dashboard-sidebar__rail"></div>

    <div class="portal-dashboard-sidebar__panel">
        <div class="portal-dashboard-brand">
            <span class="portal-dashboard-brand__mark">S</span>
            <span class="portal-dashboard-brand__copy">
                <span class="portal-dashboard-brand__name">{{ config('app.name', 'Sarunis') }}</span>
                <span class="portal-dashboard-brand__caption">Portal Sekolah</span>
            </span>
        </div>

        @if ($userRoles->isNotEmpty())
            <div class="portal-dashboard-role-badge" title="{{ $userRoles->implode(' + ') }}">
                {{ $userRoles->implode(' + ') }}
            </div>
        @endif

        <div class="portal-dashboard-menu-groups">
            @foreach ($menuSections as $section)
                @if (! empty($section['items']))
                    <div class="portal-dashboard-menu-group">
                        <div class="portal-dashboard-menu-section">{{ $section['title'] }}</div>

                        <nav class="portal-dashboard-menu-list" aria-label="{{ $section['title'] }}">
                            @foreach ($section['items'] as $item)
                                <a class="portal-dashboard-menu-item {{ $item['active'] ? 'is-active' : '' }}" href="{{ $item['href'] }}" title="{{ $item['label'] }}" @if (($interactiveSidebar ?? false) && str_starts_with($item['href'], '#')) data-nav-link @endif>
                                    <span class="portal-dashboard-menu-item__icon" aria-hidden="true">
                                        @include('dashboard.partials.icon', ['name' => $item['icon']])
                                    </span>
                                    <span class="portal-dashboard-menu-item__label">{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </nav>
                    </div>
                @endif
            @endforeach
        </div>

        <form method="POST" action="{{ url('/logout') }}" class="mt-auto">
            @csrf
            <button class="portal-dashboard-menu-item portal-dashboard-menu-item--button" type="submit">
                <span class="portal-dashboard-menu-item__icon" aria-hidden="true">
                    @include('dashboard.partials.icon', ['name' => 'logout'])
                </span>
                <span class="portal-dashboard-menu-item__label">Keluar</span>
            </button>
        </form>
    </div>
</aside>
