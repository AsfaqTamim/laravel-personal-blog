<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the settings form.
     */
    public function index(): View
    {
        $settings = [
            'site_name' => Setting::get('site_name', config('app.name', 'Clara Dawson Blogs')),
            'site_tagline' => Setting::get('site_tagline', 'Stories, ideas & insights on all kinds of topics'),
            'posts_per_page' => (int) Setting::get('posts_per_page', 6),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Save the settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'posts_per_page' => ['required', 'integer', 'min:3', 'max:50'],
        ]);

        Setting::set('site_name', $data['site_name']);
        Setting::set('site_tagline', $data['site_tagline'] ?? '');
        Setting::set('posts_per_page', (string) $data['posts_per_page']);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings saved successfully.');
    }

    /**
     * Show the SEO settings form.
     */
    public function seo(): View
    {
        $settings = [
            'seo_description' => Setting::get('seo_description', ''),
            'seo_keywords' => Setting::get('seo_keywords', ''),
            'seo_og_image' => Setting::get('seo_og_image', ''),
            'seo_author' => Setting::get('seo_author', 'Clara Dawson'),
            'seo_robots' => Setting::get('seo_robots', 'index, follow'),
            'google_site_verification' => Setting::get('google_site_verification', ''),
            'bing_site_verification' => Setting::get('bing_site_verification', ''),
        ];

        return view('admin.settings.seo', compact('settings'));
    }

    /**
     * Save the SEO settings.
     */
    public function updateSeo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'seo_description' => ['nullable', 'string', 'max:255'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'seo_og_image' => ['nullable', 'url:http,https', 'max:2048'],
            'seo_author' => ['nullable', 'string', 'max:100'],
            'seo_robots' => [
                'required',
                Rule::in(['index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow']),
            ],
            'google_site_verification' => ['nullable', 'string', 'max:255'],
            'bing_site_verification' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return redirect()
            ->route('admin.settings.seo')
            ->with('success', 'SEO settings saved successfully.');
    }
}
