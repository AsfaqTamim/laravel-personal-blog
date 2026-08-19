<?php

namespace Tests\Unit;

use App\Models\Visit;
use PHPUnit\Framework\TestCase;

class BotDetectionTest extends TestCase
{
    public function test_bot_user_agents_are_detected(): void
    {
        $this->assertTrue(Visit::isBot('Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'));
        $this->assertTrue(Visit::isBot('curl/8.4.0'));
        $this->assertTrue(Visit::isBot(null));
        $this->assertTrue(Visit::isBot(''));
    }

    public function test_human_user_agents_are_not_bots(): void
    {
        $this->assertFalse(Visit::isBot('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36'));
        $this->assertFalse(Visit::isBot('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile Safari/604.1'));
    }
}
