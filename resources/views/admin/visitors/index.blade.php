@extends('admin.layouts.app')

@section('title', 'Visitors')
@section('topbar-title', 'Visitors')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Visitor Analytics</h1>
            <p class="page-sub">Visitors are identified via a browser cookie — bots and repeat visits from the same IP are filtered out.</p>
        </div>
        <div class="page-actions">
            <form
                method="POST"
                action="{{ route('admin.visitors.clear') }}"
                onsubmit="return confirm('Clear all visitor statistics? This cannot be undone.');"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Clear Stats</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Visitors Today</span>
                <span class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['visitors_today'] }}</div>
            <div class="stat-hint">{{ $stats['new_today'] }} new · {{ $stats['returning_today'] }} returning</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Unique Visitors</span>
                <span class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a7.464 7.464 0 01-1.15 3.993m1.989 3.559A11.209 11.209 0 008.25 10.5a3.75 3.75 0 117.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 01-3.6 9.75m6.633-4.596a18.666 18.666 0 01-2.485 5.33" />
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['visitors_total'] }}</div>
            <div class="stat-hint">all time</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Visits Today</span>
                <span class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 012.25-2.25h13.5a2.25 2.25 0 012.25 2.25v11.251m-18 0a2.25 2.25 0 002.25 2.25h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5a2.25 2.25 0 012.25-2.25h13.5a2.25 2.25 0 012.25 2.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['visits_today'] }}</div>
            <div class="stat-hint">page views today</div>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <span class="stat-label">Total Visits</span>
                <span class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['visits_total'] }}</div>
            <div class="stat-hint">all time</div>
        </div>
    </div>

    <div class="dash-grid">
        {{-- 14-day chart --}}
        <div class="panel">
            <div class="panel-head">
                <i class="fa-solid fa-chart-column" aria-hidden="true"></i> Visits — Last 14 Days
            </div>
            <div class="panel-body">
                @if (! $chartHasData)
                    <div class="empty-cell">No visits recorded yet — traffic appears here automatically.</div>
                @else
                    <div class="vchart" aria-label="Visits per day for the last 14 days">
                        @foreach ($chart as $day)
                            <div class="vbar" title="{{ $day['label'] }}: {{ $day['total'] }} visit(s)">
                                @if ($day['total'] > 0)
                                    <span class="vbar-count">{{ $day['total'] }}</span>
                                @endif
                                <div class="vbar-fill" style="height: {{ (int) round($day['total'] / $chartMax * 100) }}%;"></div>
                                <span class="vbar-label">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Devices + network --}}
        <div class="panel-stack">
            <div class="panel">
                <div class="panel-head">
                    <i class="fa-solid fa-display" aria-hidden="true"></i> Devices
                </div>
                <div class="panel-body">
                    <div class="breakdown-row">
                        <span class="breakdown-icon"><i class="fa-solid fa-display" aria-hidden="true"></i></span>
                        <span class="breakdown-name">Desktop</span>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $devices['desktop']['percent'] }}%;"></div></div>
                        <span class="breakdown-count">{{ $devices['desktop']['count'] }} · {{ $devices['desktop']['percent'] }}%</span>
                    </div>
                    <div class="breakdown-row">
                        <span class="breakdown-icon"><i class="fa-solid fa-mobile-screen" aria-hidden="true"></i></span>
                        <span class="breakdown-name">Mobile</span>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $devices['mobile']['percent'] }}%;"></div></div>
                        <span class="breakdown-count">{{ $devices['mobile']['count'] }} · {{ $devices['mobile']['percent'] }}%</span>
                    </div>
                    <div class="breakdown-row">
                        <span class="breakdown-icon"><i class="fa-solid fa-tablet" aria-hidden="true"></i></span>
                        <span class="breakdown-name">Tablet</span>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $devices['tablet']['percent'] }}%;"></div></div>
                        <span class="breakdown-count">{{ $devices['tablet']['count'] }} · {{ $devices['tablet']['percent'] }}%</span>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <i class="fa-solid fa-wifi" aria-hidden="true"></i> Network
                </div>
                <div class="panel-body">
                    <div class="breakdown-row">
                        <span class="breakdown-icon"><i class="fa-solid fa-wifi" aria-hidden="true"></i></span>
                        <span class="breakdown-name">Wi-Fi</span>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $networks['wifi']['percent'] }}%;"></div></div>
                        <span class="breakdown-count">{{ $networks['wifi']['count'] }} · {{ $networks['wifi']['percent'] }}%</span>
                    </div>
                    <div class="breakdown-row">
                        <span class="breakdown-icon"><i class="fa-solid fa-signal" aria-hidden="true"></i></span>
                        <span class="breakdown-name">Cellular</span>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $networks['cellular']['percent'] }}%;"></div></div>
                        <span class="breakdown-count">{{ $networks['cellular']['count'] }} · {{ $networks['cellular']['percent'] }}%</span>
                    </div>
                    <div class="breakdown-row">
                        <span class="breakdown-icon"><i class="fa-solid fa-circle-question" aria-hidden="true"></i></span>
                        <span class="breakdown-name">Unknown</span>
                        <div class="bar-track"><div class="bar-fill" style="width: {{ $networks['unknown']['percent'] }}%;"></div></div>
                        <span class="breakdown-count">{{ $networks['unknown']['count'] }} · {{ $networks['unknown']['percent'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Locations --}}
    <div class="panel">
        <div class="panel-head">
            <i class="fa-solid fa-location-dot" aria-hidden="true"></i> Top Locations
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>City</th>
                        <th>Visitors</th>
                        <th>Visits</th>
                        <th>Last Visit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($locations as $location)
                        <tr>
                            <td class="cell-title">{{ $location->country }}</td>
                            <td>{{ $location->city ?? '—' }}</td>
                            <td>{{ $location->visitors }}</td>
                            <td>{{ $location->visits }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($location->last_visit)->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-cell">
                                Location data appears once visits come from public IP addresses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent visits --}}
    <div class="panel">
        <div class="panel-head">
            <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i> Recent Visits
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Visited</th>
                        <th>IP Address</th>
                        <th>Device</th>
                        <th>Browser</th>
                        <th>Network</th>
                        <th>Location</th>
                        <th>Page</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recent as $visit)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $visit->visited_at->format('M d, H:i') }}</div>
                                <div class="cell-sub">{{ $visit->visited_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="cell-title">{{ $visit->ip_address }}</div>
                                <div class="cell-sub">
                                    @if ($visit->visit_token && in_array($visit->visit_token, $returningTokens, true))
                                        Returning visitor
                                    @else
                                        New visitor
                                    @endif
                                </div>
                            </td>
                            <td><span class="chip">{{ ucfirst($visit->device_type) }}</span></td>
                            <td>{{ $visit->browser ?? '—' }}</td>
                            <td>
                                @if ($visit->network_type === 'wifi')
                                    <span class="chip"><i class="fa-solid fa-wifi" aria-hidden="true"></i> Wi-Fi</span>
                                @elseif ($visit->network_type === 'cellular')
                                    <span class="chip"><i class="fa-solid fa-signal" aria-hidden="true"></i> Cellular</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($visit->city && $visit->country)
                                    {{ $visit->city }}, {{ $visit->country }}
                                @elseif ($visit->country)
                                    {{ $visit->country }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="comment-body">
                                    {{ $visit->page_url ? Str::limit($visit->page_url, 60) : '—' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-cell">No visits recorded yet. Traffic appears here automatically.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($recent->hasPages())
            <div class="panel-foot">
                {{ $recent->links() }}
            </div>
        @endif
    </div>
@endsection
