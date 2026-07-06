<section class="portal-panel portal-calendar-card" data-calendar data-current-month="{{ now()->format('Y-m-01') }}">
    <div class="portal-calendar-card__header">
        <button type="button" aria-label="Bulan sebelumnya" data-calendar-nav="-1">
            @include('dashboard.partials.icon', ['name' => 'chevron-left'])
        </button>
        <h3 data-calendar-label>{{ $calendar['month_label'] }}</h3>
        <button type="button" aria-label="Bulan berikutnya" data-calendar-nav="1">
            @include('dashboard.partials.icon', ['name' => 'chevron-right'])
        </button>
    </div>

    <div class="portal-calendar-grid portal-calendar-grid--head">
        @foreach ($calendar['days'] as $dayLabel)
            <span>{{ $dayLabel }}</span>
        @endforeach
    </div>

    <div data-calendar-body>
        @foreach ($calendar['weeks'] as $week)
            <div class="portal-calendar-grid">
                @foreach ($week as $day)
                    <span class="portal-calendar-day {{ $day['is_current_month'] ? '' : 'is-outside' }} {{ $day['is_today'] ? 'is-today' : '' }}">
                        {{ $day['day'] }}
                    </span>
                @endforeach
            </div>
        @endforeach
    </div>
</section>
