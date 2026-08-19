<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorTest extends TestCase
{
    use RefreshDatabase;

    private function mobileUserAgent(): string
    {
        return 'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Mobile Safari/537.36';
    }

    public function test_public_pages_are_tracked_as_visits(): void
    {
        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.index'))
            ->assertOk();

        $visit = Visit::first();
        $this->assertNotNull($visit);
        $this->assertSame('mobile', $visit->device_type);
        $this->assertSame('Chrome', $visit->browser);
        $this->assertSame(url('/'), $visit->page_url);
    }

    public function test_bot_requests_are_not_tracked(): void
    {
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])
            ->get(route('blog.index'))
            ->assertOk();

        $this->assertSame(0, Visit::count());
    }

    public function test_repeat_visits_from_same_ip_are_deduplicated(): void
    {
        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.index'))
            ->assertOk();

        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.all'))
            ->assertOk();

        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.index'))
            ->assertOk();

        $this->assertSame(1, Visit::count());
    }

    public function test_first_visit_sets_a_visitor_cookie(): void
    {
        $response = $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.index'));

        $response->assertOk()->assertCookie('visit_token');

        $visit = Visit::first();
        $this->assertNotNull($visit->visit_token);
        $this->assertSame(32, strlen((string) $visit->visit_token));
    }

    public function test_returning_visitor_is_counted_again_after_dedupe_window(): void
    {
        $token = 'returning-visitor-1234567890abcdef';

        Visit::create([
            'visit_token' => $token,
            'ip_address' => '127.0.0.1',
            'device_type' => 'desktop',
            'visited_at' => now()->subHour(),
        ]);

        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->withCookie('visit_token', $token)
            ->get(route('blog.index'))
            ->assertOk();

        $this->assertSame(2, Visit::count());
        $this->assertSame(2, Visit::where('visit_token', $token)->count());
    }

    public function test_visitor_stats_distinguish_new_and_returning_visitors(): void
    {
        $returning = 'returning-visitor-1234567890abcdef';

        Visit::create([
            'visit_token' => $returning,
            'ip_address' => '192.0.2.1',
            'device_type' => 'desktop',
            'visited_at' => now()->subDay(),
        ]);
        Visit::create([
            'visit_token' => $returning,
            'ip_address' => '192.0.2.1',
            'device_type' => 'desktop',
            'visited_at' => now(),
        ]);
        Visit::create([
            'visit_token' => 'new-visitor-1234567890abcdef',
            'ip_address' => '192.0.2.2',
            'device_type' => 'mobile',
            'visited_at' => now(),
        ]);

        $admin = User::factory()->create(['email' => 'admin@dml-blog.test']);

        $this->actingAs($admin)
            ->get(route('admin.visitors.index'))
            ->assertOk()
            ->assertSee('1 new · 1 returning');
    }

    public function test_machine_endpoints_are_not_tracked(): void
    {
        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.sitemap'))
            ->assertOk();

        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.robots'))
            ->assertOk();

        $this->assertSame(0, Visit::count());
    }

    public function test_network_type_ping_updates_the_latest_visit(): void
    {
        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.index'))
            ->assertOk();

        $this->get('/visit-network?type=wifi')->assertNoContent();

        $this->assertSame('wifi', Visit::first()->network_type);
    }

    public function test_network_type_ping_rejects_unknown_types(): void
    {
        $this->withHeaders(['User-Agent' => $this->mobileUserAgent()])
            ->get(route('blog.index'))
            ->assertOk();

        $this->get('/visit-network?type=bluetooth')->assertNoContent();

        $this->assertNull(Visit::first()->network_type);
    }

    public function test_admin_can_view_visitor_analytics(): void
    {
        Visit::create([
            'ip_address' => '203.0.113.10',
            'device_type' => 'desktop',
            'browser' => 'Firefox',
            'network_type' => 'wifi',
            'country' => 'Germany',
            'region' => 'Bavaria',
            'city' => 'Munich',
            'page_url' => 'http://localhost/',
            'visited_at' => now(),
        ]);

        $admin = User::factory()->create(['email' => 'admin@dml-blog.test']);

        $this->actingAs($admin)
            ->get(route('admin.visitors.index'))
            ->assertOk()
            ->assertSee('Visitor Analytics')
            ->assertSee('Germany')
            ->assertSee('Munich')
            ->assertSee('Desktop')
            ->assertSee('Wi-Fi');
    }

    public function test_admin_can_clear_visitor_stats(): void
    {
        Visit::factory()->count(3)->create();

        $admin = User::factory()->create(['email' => 'admin@dml-blog.test']);

        $this->actingAs($admin)
            ->delete(route('admin.visitors.clear'))
            ->assertRedirect(route('admin.visitors.index'));

        $this->assertSame(0, Visit::count());
    }

    public function test_recent_visits_table_is_paginated(): void
    {
        Visit::factory()->count(25)->create([
            'visited_at' => now()->subDays(2),
        ]);

        $oldest = Visit::factory()->create([
            'ip_address' => '203.0.113.99',
            'visited_at' => now()->subDays(3),
        ]);

        $admin = User::factory()->create(['email' => 'admin@dml-blog.test']);

        $this->actingAs($admin)
            ->get(route('admin.visitors.index', ['page' => 2]))
            ->assertOk()
            ->assertSee($oldest->ip_address);
    }
}
