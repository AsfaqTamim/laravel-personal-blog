<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'site_logo' => Setting::get('site_logo', ''),
            'site_favicon' => Setting::get('site_favicon', ''),
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
            'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:1024'],
            'site_favicon' => ['nullable', 'mimes:png,jpg,jpeg,ico,svg,webp', 'max:512'],
        ]);

        Setting::set('site_name', $data['site_name']);
        Setting::set('site_tagline', $data['site_tagline'] ?? '');
        Setting::set('posts_per_page', (string) $data['posts_per_page']);

        if ($request->hasFile('site_logo')) {
            $this->storeImage('site_logo', $request->file('site_logo'));
        }

        if ($request->hasFile('site_favicon')) {
            $this->storeImage('site_favicon', $request->file('site_favicon'));
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings saved successfully.');
    }

    /**
     * Store an uploaded logo/favicon and clean up the previous one.
     */
    private function storeImage(string $key, $file): void
    {
        $old = Setting::get($key, '');
        $new = $file->store('site', 'public');

        if ($old !== '' && $old !== $new && str_starts_with($old, 'site/')) {
            Storage::disk('public')->delete($old);
        }

        Setting::set($key, $new);
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
