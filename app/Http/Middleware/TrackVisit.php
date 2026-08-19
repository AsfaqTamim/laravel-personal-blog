<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $this->maybeRecord($request);

        return $response;
    }

    /**
     * Record the visit unless it should be filtered out.
     */
    private function maybeRecord(Request $request): void
    {
        if (! $this->shouldTrack($request)) {
            return;
        }

        $token = $request->cookie('visit_token');

        if ($token !== null && $token !== '') {
            // The cookie identifies this visitor — dedupe by token.
            $alreadyRecorded = Visit::where('visit_token', $token)
                ->where('visited_at', '>=', now()->subMinutes(Visit::DEDUPE_MINUTES))
                ->exists();
        } else {
            // No cookie yet (new or cookieless client) — dedupe by IP.
            $alreadyRecorded = Visit::where('ip_address', $request->ip())
                ->where('visited_at', '>=', now()->subMinutes(Visit::DEDUPE_MINUTES))
                ->exists();
        }

        if ($alreadyRecorded) {
            return;
        }

        // Hand out an identifier so this visitor is recognized next time.
        if ($token === null || $token === '') {
            $token = Str::random(32);
            Cookie::queue('visit_token', $token, 60 * 24 * 365 * 2);
        }

        $ip = $request->ip();

        try {
            $geo = Visit::geolocate($ip);
        } catch (\Throwable) {
            $geo = null;
        }

        $ua = $request->userAgent();

        Visit::create([
            'visit_token' => $token,
            'ip_address' => $ip,
            'device_type' => $this->deviceType($ua),
            'browser' => $this->browser($ua),
            'network_type' => null,
            'country' => $geo['country'] ?? null,
            'region' => $geo['region'] ?? null,
            'city' => $geo['city'] ?? null,
            'page_url' => mb_substr($request->fullUrl(), 0, 255),
            'referrer' => mb_substr((string) $request->headers->get('referer'), 0, 255) ?: null,
            'visited_at' => now(),
        ]);
    }

    /**
     * Whether this request counts as a human page view.
     */
    private function shouldTrack(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->ajax() || $request->prefetch() || $request->expectsJson()) {
            return false;
        }

        // These are machine-read endpoints, not page visits.
        if ($request->is('sitemap.xml', 'robots.txt', 'feed', 'visit-network')) {
            return false;
        }

        $ua = $request->userAgent();
        if (Visit::isBot($ua)) {
            return false;
        }

        return true;
    }

    /**
     * Detect the device type from the user agent.
     */
    private function deviceType(?string $ua): string
    {
        if ($ua === null) {
            return 'desktop';
        }

        if (preg_match('/ipad|tablet|playbook|silk/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/android|iphone|ipod|windows phone|iemobile|blackberry|opera mini|mobile/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Detect the browser name from the user agent.
     */
    private function browser(?string $ua): ?string
    {
        if ($ua === null) {
            return null;
        }

        return match (true) {
            (bool) preg_match('/edg\//i', $ua) => 'Edge',
            (bool) preg_match('/(chrome|crios)\//i', $ua) => 'Chrome',
            (bool) preg_match('/firefox|fxios/i', $ua) => 'Firefox',
            (bool) preg_match('/safari/i', $ua) => 'Safari',
            (bool) preg_match('/(opera|opios|opr\/)/i', $ua) => 'Opera',
            default => null,
        };
    }
}
