<?php

declare(strict_types=1);

namespace Shammaa\IntelligentCache\Traits;

use Shammaa\IntelligentCache\Services\IntelligentCacheService;

trait HasSmartCache
{
    /**
     * Boot the trait and register model event listeners.
     */
    protected static function bootHasSmartCache(): void
    {
        static::saved(function ($model) {
            static::clearIntelligentCache();
        });

        static::deleted(function ($model) {
            static::clearIntelligentCache();
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                static::clearIntelligentCache();
            });
        }
    }

    /**
     * Clear the cache associated with this model.
     */
    public static function clearIntelligentCache(): void
    {
        app(IntelligentCacheService::class)->clear();
        
        // Cache warming logic can be added here later
        // (e.g., dispatch a job to crawl important pages)
    }
}
