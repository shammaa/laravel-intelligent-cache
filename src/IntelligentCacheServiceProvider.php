<?php

declare(strict_types=1);

namespace Shammaa\IntelligentCache;

use Illuminate\Support\ServiceProvider;
use Shammaa\IntelligentCache\Services\IntelligentCacheService;
use Shammaa\IntelligentCache\Http\Middleware\CacheResponse;

class IntelligentCacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/intelligent-cache.php',
            'intelligent-cache'
        );

        $this->app->singleton(IntelligentCacheService::class, function ($app) {
            return new IntelligentCacheService(config('intelligent-cache'));
        });

        $this->app->alias(IntelligentCacheService::class, 'intelligent-cache');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/intelligent-cache.php' => config_path('intelligent-cache.php'),
            ], 'intelligent-cache-config');
        }

        // Register Middleware
        $this->app['router']->aliasMiddleware('smart_cache', CacheResponse::class);
    }
}
