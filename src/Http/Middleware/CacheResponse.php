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
        // 1. Generate unique key for this request
        $key = $this->cacheService->getCacheKey($request);

        // 2. If cached, return immediately
        if (Cache::has($key)) {
            $cachedContent = Cache::get($key);
            $response = response($cachedContent);
            
            if (config('intelligent-cache.headers.add_cache_status_header')) {
                $response->headers->set('X-Cache', 'HIT');
            }
            
            $this->applySmartHeaders($response);
            return $response;
        }

        // 3. If not cached, proceed with request
        $response = $next($request);

        // 4. If response is cacheable, store it
        if ($this->cacheService->shouldCache($request, $response)) {
            Cache::put($key, $response->getContent(), config('intelligent-cache.lifetime'));
            
            if (config('intelligent-cache.headers.add_cache_status_header')) {
                $response->headers->set('X-Cache', 'MISS');
            }
        }

        // 5. Apply smart headers (Solve no-cache issue)
        $this->applySmartHeaders($response);

        return $response;
    }

    /**
     * Solve the Cache-Control issue.
     */
    protected function applySmartHeaders(Response $response): void
    {
        $cacheControl = config('intelligent-cache.headers.cache_control');
        $response->headers->set('Cache-Control', $cacheControl);
        
        // Remove headers that prevent caching if set by other servers
        $response->headers->remove('Pragma');
        $response->headers->remove('Expires');
    }
}
