<?php

use App\Models\Setting;

if (! function_exists('blog_setting')) {
    /**
     * Read a blog setting with a fallback default.
     */
    function blog_setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('json_ld')) {
    /**
     * Encode a value for safe embedding inside a <script type="application/ld+json"> block.
     */
    function json_ld(mixed $value): string
    {
        return json_encode(
            $value,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
