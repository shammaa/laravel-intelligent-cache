<?php

return [
    /*
     * Enable or disable the entire caching system.
     */
    'enabled' => env('INTELLIGENT_CACHE_ENABLED', true),

    /*
     * Default cache lifetime in seconds.
     * 3600 = 1 hour.
     */
    'lifetime' => env('INTELLIGENT_CACHE_LIFETIME', 3600),

    /*
     * Smart cache invalidation feature.
     * When a specific model is updated, the associated cache will be cleared automatically.
     */
    'auto_invalidation' => [
        'enabled' => true,
        'models' => [
            // Example: 'App\Models\Article' => ['articles', 'home'],
        ],
    ],

    /*
     * Routes that should never be cached (e.g., admin panels).
     */
    'exclude' => [
        'admin/*',
        'login',
        'register',
        'api/user',
    ],

    /*
     * Header settings to improve SEO and browser caching performance.
     */
    'headers' => [
        'cache_control' => 'public, max-age=3600, must-revalidate',
        'add_cache_status_header' => true, // Adds X-Cache: HIT or MISS to the response
        'force_headers' => true, // Forcefully overwrite other middleware headers (like session no-cache)
    ],

    /*
     * Cache driver for storing responses.
     * Recommended: file or redis.
     */
    'driver' => env('INTELLIGENT_CACHE_DRIVER', 'file'),
];
