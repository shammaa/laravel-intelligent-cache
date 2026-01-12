<?php

declare(strict_types=1);

namespace Shammaa\IntelligentCache\Traits;

use Shammaa\IntelligentCache\Services\IntelligentCacheService;

trait HasSmartCache
{
    /**
     * يتم استدعاء هذه الدالة تلقائياً بواسطة لارافيل عند إقلاع الموديل
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
     * مسح الكاش المرتبط بهذا الموديل
     */
    public static function clearIntelligentCache(): void
    {
        app(IntelligentCacheService::class)->clear();
        
        // هنا يمكن لاحقاً إضافة "تسخين الكاش" (Cache Warming)
        // أي أن السيرفر يزور الصفحات المهمة برمجياً ليخزنها من جديد
    }
}
