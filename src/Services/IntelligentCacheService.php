<?php

declare(strict_types=1);

namespace Shammaa\IntelligentCache\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Response;

class IntelligentCacheService
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * توليد مفتاح فريد لكل صفحة بناءً على الرابط والمتغيرات
     */
    public function getCacheKey($request): string
    {
        $url = $request->fullUrl();
        $method = $request->method();
        
        // نأخذ في الاعتبار اللغة لو كانت موجودة في الجلسة أو الرابط
        $locale = app()->getLocale();
        
        return 'smart_cache_' . md5($url . $method . $locale);
    }

    /**
     * هل يجب تخزين هذا الطلب؟
     */
    public function shouldCache($request, $response): bool
    {
        if (!$this->config['enabled']) return false;
        
        // نخزن فقط طلبات GET الناجحة
        if (!$request->isMethod('GET') || !$response->isSuccessful()) {
            return false;
        }

        // التأكد أن الرابط ليس مستبعداً
        foreach ($this->config['exclude'] as $pattern) {
            if ($request->is($pattern)) return false;
        }

        return true;
    }

    /**
     * مسح الكاش بالكامل أو لمسار معين
     */
    public function clear(): bool
    {
        // هذه ستمسح كل ما يبدأ بـ smart_cache_
        // ملاحظة: في Laravel الـ file driver لا يدعم مسح جزء معين بسهولة 
        // سنستخدم نظام تتبع لاحقاً، حالياً سنقوم بمسح الكاش العام للتأكد من ظهور المقالات
        return Cache::flush(); 
    }

    /**
     * مسح ذكي عند تحديث مقال
     */
    public function forgetForModel(string $modelClass): void
    {
        // هنا سنضيف المنطق الذي يمسح صفحات معينة مرتبطة بالموديل
        // حالياً سنمسح الكاش لضمان التحديث الفوري كما طلبت
        $this->clear();
    }
}
