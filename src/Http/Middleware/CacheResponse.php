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
        // 1. Only handle GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // 2. Quick check for common image/binary extensions to skip early
        if (preg_match('/\.(jpg|jpeg|png|gif|svg|webp|pdf|zip|css|js)$/i', $request->getPathInfo())) {
            return $next($request);
        }

        $key = $this->cacheService->getCacheKey($request);

        // 3. If cached, return immediately
        if (Cache::has($key)) {
            $cachedContent = Cache::get($key);
            $response = response($cachedContent);
            
            if (config('intelligent-cache.headers.add_cache_status_header')) {
                $response->headers->set('X-Cache', 'HIT');
            }
            
            $this->applySmartHeaders($response);
            return $response;
        }

        $response = $next($request);

        // 4. If response is cacheable, store it and apply headers
        if ($this->cacheService->shouldCache($request, $response)) {
            Cache::put($key, $response->getContent(), config('intelligent-cache.lifetime'));
            
            if (config('intelligent-cache.headers.add_cache_status_header')) {
                $response->headers->set('X-Cache', 'MISS');
            }

            $this->applySmartHeaders($response);
        }

        return $response;
    }

    /**
     * Solve the Cache-Control issue for HTML responses only.
     */
    protected function applySmartHeaders(Response $response): void
    {
        // Double check it's HTML before touching headers
        $contentType = $response->headers->get('Content-Type');
        if (str_contains((string) $contentType, 'text/html')) {
            $cacheControl = config('intelligent-cache.headers.cache_control');
            
            // 1. Forcefully CLEAR any existing cache headers set by other middlewares or PHP
            $response->headers->remove('Cache-Control');
            $response->headers->remove('Pragma');
            $response->headers->remove('Expires');

            // 2. Set our optimized headers
            $response->headers->set('Cache-Control', $cacheControl);
            
            // Optional: Ensure No-Cache doesn't leak from PHP session_cache_limiter
            if (function_exists('header_remove')) {
                header_remove('Pragma');
            }
        }
    }
}
