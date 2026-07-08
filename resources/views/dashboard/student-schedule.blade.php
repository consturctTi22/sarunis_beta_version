@extends('layouts.portal-dashboard')

@section('title', $pageTitle)

@section('content')
    <div class="portal-dashboard-shell" data-dashboard data-dashboard-portal="{{ $portalKey }}">
        @include('dashboard.partials.sidebar', ['menuSections' => $menuSections, 'interactiveSidebar' => true])

        <main class="portal-dashboard-main portal-directory-main">
            <div class="portal-directory-header">
                <div>
                    <h1>{{ $pageTitle }}</h1>
                    <p>Seluruh jadwal mata pelajaran dari Senin hingga Jumat.</p>
                </div>
            </div>

            @if (!empty($allScheduleRows))
            <section class="portal-panel portal-schedule-full-table mt-4">
                @foreach ($allScheduleRows as $dayGroup)
                <div class="portal-schedule-day-group mb-5">
                    <h3 class="portal-schedule-day-label" style="font-size: 1rem; font-weight: 600; color: var(--portal-text-primary, #fff); padding: 10px 16px; background: rgba(115, 103, 240, 0.12); border-radius: 8px; margin-bottom: 16px; display: inline-block;">
                        📅 {{ $dayGroup['day'] }}
                    </h3>

                    <div class="table-responsive">
                        <table class="table portal-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Jam Ke-</th>
                                    <th style="width: 140px;">Jam Mapel</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    <th>Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dayGroup['items'] as $item)
                                <tr data-search-item>
                                    <td><span class="portal-badge is-primary">{{ $item['lesson_period'] }}</span></td>
                                    <td>{{ $item['time'] }}</td>
                                    <td><strong>{{ $item['subject'] }}</strong></td>
                                    <td>{{ $item['teacher'] }}</td>
                                    <td>{{ $item['room'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </section>
            @else
            <section class="portal-panel mt-4">
                <div class="text-center text-muted py-5">
                    Belum ada jadwal mata pelajaran yang terdaftar untuk Anda saat ini.
                </div>
            </section>
            @endif

        </main>
    </div>
@endsection
