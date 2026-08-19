<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VisitorController extends Controller
{
    /**
     * Show visitor analytics.
     */
    public function index(): View
    {
        $today = now()->startOfDay();

        // A visitor is identified by their cookie token (or IP as a
        // fallback for cookieless clients / legacy rows).
        $identity = DB::raw("COALESCE(visit_token, CONCAT('ip:', ip_address))");

        $visitorsToday = Visit::where('visited_at', '>=', $today)->distinct()->count($identity);
        $visitorsTotal = Visit::distinct()->count($identity);

        $earlierTokens = Visit::where('visited_at', '<', $today)
            ->whereNotNull('visit_token')
            ->select('visit_token')
            ->distinct();

        $returningToday = Visit::where('visited_at', '>=', $today)
            ->whereNotNull('visit_token')
            ->whereIn('visit_token', $earlierTokens)
            ->distinct()
            ->count('visit_token');

        $stats = [
            'visitors_today' => $visitorsToday,
            'new_today' => max(0, $visitorsToday - $returningToday),
            'returning_today' => $returningToday,
            'visitors_total' => $visitorsTotal,
            'visits_today' => Visit::where('visited_at', '>=', $today)->count(),
            'visits_total' => Visit::count(),
        ];

        $deviceRows = Visit::select('device_type', DB::raw('count(*) as total'))
            ->groupBy('device_type')
            ->pluck('total', 'device_type');

        $networkRows = Visit::select('network_type', DB::raw('count(*) as total'))
            ->groupBy('network_type')
            ->pluck('total', 'network_type');

        $dailyRows = Visit::select(DB::raw('DATE(visited_at) as day'), DB::raw('count(*) as total'))
            ->where('visited_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->pluck('total', 'day');

        $chart = collect(range(13, 0))->map(function (int $offset) use ($dailyRows) {
            $day = now()->subDays($offset);

            return [
                'label' => $day->format('M j'),
                'total' => (int) $dailyRows->get($day->toDateString(), 0),
            ];
        });

        $locations = Visit::whereNotNull('country')
            ->select(
                'country',
                'city',
                DB::raw('count(*) as visits'),
                DB::raw('count(distinct ip_address) as visitors'),
                DB::raw('max(visited_at) as last_visit')
            )
            ->groupBy('country', 'city')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        $recent = Visit::orderByDesc('visited_at')->paginate(20);

        // Tokens that appear in more than one visit = returning visitors.
        $returningTokens = Visit::whereIn('visit_token', $recent->pluck('visit_token')->filter()->unique()->values())
            ->select('visit_token', DB::raw('count(*) as total'))
            ->groupBy('visit_token')
            ->havingRaw('count(*) >= 2')
            ->pluck('visit_token')
            ->all();

        return view('admin.visitors.index', [
            'stats' => $stats,
            'devices' => $this->breakdown([
                'desktop' => (int) $deviceRows->get('desktop', 0),
                'mobile' => (int) $deviceRows->get('mobile', 0),
                'tablet' => (int) $deviceRows->get('tablet', 0),
            ]),
            'networks' => $this->breakdown([
                'wifi' => (int) $networkRows->get('wifi', 0),
                'cellular' => (int) $networkRows->get('cellular', 0),
                'unknown' => (int) $networkRows->get(null, 0),
            ]),
            'chart' => $chart,
            'chartHasData' => $chart->sum('total') > 0,
            'chartMax' => max(1, (int) $chart->max('total')),
            'locations' => $locations,
            'recent' => $recent,
            'returningTokens' => $returningTokens,
        ]);
    }

    /**
     * Delete all visitor records.
     */
    public function clear(): RedirectResponse
    {
        Visit::query()->delete();

        return redirect()->route('admin.visitors.index')->with('success', 'Visitor statistics cleared.');
    }

    /**
     * Turn raw counts into count + percentage pairs.
     *
     * @param  array<string, int>  $counts
     * @return array<string, array{count: int, percent: int}>
     */
    private function breakdown(array $counts): array
    {
        $total = array_sum($counts);

        $result = [];
        foreach ($counts as $key => $count) {
            $result[$key] = [
                'count' => $count,
                'percent' => $total > 0 ? (int) round($count / $total * 100) : 0,
            ];
        }

        return $result;
    }
}
