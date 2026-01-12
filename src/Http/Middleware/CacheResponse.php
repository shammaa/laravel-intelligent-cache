<?php

declare(strict_types=1);

namespace Shammaa\IntelligentCache\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Shammaa\IntelligentCache\Services\IntelligentCacheService;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    protected IntelligentCacheService $cacheService;

    public function __construct(IntelligentCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        // 1. توليد المفتاح الفريد لهذه الصفحة
        $key = $this->cacheService->getCacheKey($request);

        // 2. إذا كانت الصفحة مخزنة سابقاً، أرجعها فوراً
        if (Cache::has($key)) {
            $cachedContent = Cache::get($key);
            $response = response($cachedContent);
            
            if (config('intelligent-cache.headers.add_cache_status_header')) {
                $response->headers->set('X-Cache', 'HIT');
            }
            
            $this->applySmartHeaders($response);
            return $response;
        }

        // 3. إذا لم تكن مخزنة، تابع تنفيذ الطلب
        $response = $next($request);

        // 4. إذا كانت الصفحة مؤهلة للتخزين، قم بتخزينها
        if ($this->cacheService->shouldCache($request, $response)) {
            Cache::put($key, $response->getContent(), config('intelligent-cache.lifetime'));
            
            if (config('intelligent-cache.headers.add_cache_status_header')) {
                $response->headers->set('X-Cache', 'MISS');
            }
        }

        // 5. تطبيق الـ Headers الذكية (لحل مشكلة no-cache)
        $this->applySmartHeaders($response);

        return $response;
    }

    /**
     * حل مشكلة الـ Cache-Control
     */
    protected function applySmartHeaders(Response $response): void
    {
        $cacheControl = config('intelligent-cache.headers.cache_control');
        $response->headers->set('Cache-Control', $cacheControl);
        
        // إزالة الـ Headers التي تمنع التخزين لو كتبت بواسطة سيرفرات أخرى
        $response->headers->remove('Pragma');
        $response->headers->remove('Expires');
    }
}
