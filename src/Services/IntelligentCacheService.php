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
     * Generate a unique cache key for a page based on URL and context.
     */
    public function getCacheKey($request): string
    {
        $url = $request->fullUrl();
        $method = $request->method();
        
        // Consider locale if present in session or URL
        $locale = app()->getLocale();
        
        return 'smart_cache_' . md5($url . $method . $locale);
    }

    /**
     * Determine if the request should be cached.
     */
    public function shouldCache($request, $response): bool
    {
        if (!$this->config['enabled']) return false;
        
        // Cache only successful GET requests
        if (!$request->isMethod('GET') || !$response->isSuccessful()) {
            return false;
        }

        // Ensure path is not excluded
        foreach ($this->config['exclude'] as $pattern) {
            if ($request->is($pattern)) return false;
        }

        return true;
    }

    /**
     * Clear the entire cache or specific path.
     */
    public function clear(): bool
    {
        // Flush all items starting with smart_cache_
        // Note: Laravel's file driver does not support tags easily.
        // We will use a tracking system later; for now, flush all for accuracy.
        return Cache::flush(); 
    }

    /**
     * Intelligent invalidation when a model is updated.
     */
    public function forgetForModel(string $modelClass): void
    {
        // Logic to clear specific pages related to the model
        // Currently flushing all to ensure immediate update as requested
        $this->clear();
    }
}
