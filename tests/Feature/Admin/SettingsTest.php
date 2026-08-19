<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    public function test_admin_can_update_settings(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.update'), [
                'site_name' => 'My Awesome Blog',
                'site_tagline' => 'Words and things',
                'posts_per_page' => 12,
            ])
            ->assertRedirect(route('admin.settings.index'));

        $this->assertSame('My Awesome Blog', Setting::get('site_name'));
        $this->assertSame('Words and things', Setting::get('site_tagline'));
        $this->assertSame('12', Setting::get('posts_per_page'));
    }

    public function test_site_name_setting_is_used_on_the_homepage(): void
    {
        Setting::set('site_name', 'Custom Blog Name');

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Custom Blog Name');
    }

    public function test_admin_can_update_seo_settings(): void
    {
        $this->actingAs($this->admin())
            ->put(route('admin.settings.seo.update'), [
                'seo_description' => 'Sitewide SEO description',
                'seo_keywords' => 'blog, stories',
                'seo_og_image' => 'https://example.com/og.jpg',
                'seo_author' => 'Jane Editor',
                'seo_robots' => 'index, follow',
                'google_site_verification' => 'google123',
                'bing_site_verification' => 'bing456',
            ])
            ->assertRedirect(route('admin.settings.seo'));

        $this->assertSame('Sitewide SEO description', Setting::get('seo_description'));
        $this->assertSame('google123', Setting::get('google_site_verification'));
        $this->assertSame('bing456', Setting::get('bing_site_verification'));
        $this->assertSame('Jane Editor', Setting::get('seo_author'));
    }

    public function test_seo_settings_are_used_on_the_homepage(): void
    {
        Setting::set('seo_description', 'Custom SEO description');
        Setting::set('seo_author', 'Jane Editor');
        Setting::set('google_site_verification', 'google123');

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('<meta name="description" content="Custom SEO description">', false)
            ->assertSee('<meta name="author" content="Jane Editor">', false)
            ->assertSee('<meta name="google-site-verification" content="google123">', false);
    }

    public function test_posts_without_images_fall_back_to_default_share_image(): void
    {
        Setting::set('seo_og_image', 'https://example.com/og.jpg');

        $post = Post::factory()->published()->create(['user_id' => User::factory()]);

        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('<meta property="og:image" content="https://example.com/og.jpg">', false);
    }
}
