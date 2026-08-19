<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

#[Fillable([
    'visit_token',
    'ip_address',
    'device_type',
    'browser',
    'network_type',
    'country',
    'region',
    'city',
    'page_url',
    'referrer',
    'visited_at',
])]
class Visit extends Model
{
    use HasFactory;

    /**
     * Known crawlers, spiders and non-browser clients — filtered out entirely.
     */
    private const BOT_PATTERN = '/(bot|crawler|spider|slurp|curl|wget|python-requests|postman|facebookexternalhit|whatsapp|telegrambot|bingpreview|headless|monitor|pingdom|uptimerobot|lighthouse|scanner|archive\.org|semrush|ahrefs|mj12|yandex|baiduspider|sogou|exabot|ia_archiver)/i';

    /**
     * Whether a user agent belongs to a bot/crawler.
     * Missing or empty user agents are treated as bots.
     */
    public static function isBot(?string $userAgent): bool
    {
        return $userAgent === null
            || trim($userAgent) === ''
            || preg_match(self::BOT_PATTERN, $userAgent) === 1;
    }

    /**
     * Minutes within which further requests from the same visitor
     * (same cookie token, or same IP without a cookie) are treated
     * as the same visit and not counted again.
     */
    public const DEDUPE_MINUTES = 30;

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }

    /**
     * Look up geolocation for an IP address.
     *
     * Private/reserved ranges (localhost, LAN) are skipped, and the
     * result is cached for 7 days. Never throws — returns null on any
     * failure so tracking can never break a page.
     *
     * @return array{country: ?string, region: ?string, city: ?string}|null
     */
    public static function geolocate(string $ip): ?array
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return null;
        }

        return Cache::remember('visit-geo:'.$ip, now()->addDays(7), function () use ($ip) {
            try {
                $response = Http::timeout(2)
                    ->retry(1, 200)
                    ->get('http://ip-api.com/json/'.$ip.'?fields=status,country,regionName,city');
            } catch (\Throwable) {
                return null;
            }

            if (! $response->successful() || $response->json('status') !== 'success') {
                return null;
            }

            return [
                'country' => $response->json('country') ?: null,
                'region' => $response->json('regionName') ?: null,
                'city' => $response->json('city') ?: null,
            ];
        });
    }
}
