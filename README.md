# Laravel Intelligent Cache 🚀

**Laravel Intelligent Cache** is a high-performance, context-aware response caching package. It is designed to significantly boost your application's speed and optimize SEO by intelligently managing `Cache-Control` headers and providing automatic cache invalidation.

---

## 🌟 Main Objectives

1. **Boost Speed:** Achieve near-zero TTFB (Time to First Byte) by caching full HTML responses.
2. **Improve SEO:** Automatically fix the `no-cache` issue that hurts Google rankings by providing proper caching headers.
3. **Always Fresh:** Keep content updated automatically. When you add a new article, the cache refreshes itself.

---

## 🛠 Installation

You can install the package via composer:

```bash
composer require shammaa/laravel-intelligent-cache
```

### Publish Config
```bash
php artisan vendor:publish --provider="Shammaa\IntelligentCache\IntelligentCacheServiceProvider"
```

---

## 🚀 How to Use

### 1. The "Smart" Part (Auto-Update)
Add the `HasSmartCache` trait to any Model (like `Article.php`). This will automatically clear the cache whenever a new item is added or updated.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Shammaa\IntelligentCache\Traits\HasSmartCache;

class Article extends Model
{
    use HasSmartCache;
}
```

### 2. Speed Up Your Pages
Apply the `smart_cache` middleware to your routes in `web.php`:

```php
Route::middleware(['smart_cache'])->group(function () {
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/article/{slug}', [ArticleController::class, 'show']);
});
```

---

## ⚙️ Configuration

You can find the settings in `config/intelligent-cache.php`:
- `lifetime`: How long to keep the cache (in seconds).
- `exclude`: URL patterns that should never be cached (like `/admin/*`).
- `headers`: Custom cache rules for browsers and Google.

---

## 📈 Performance Impact
- **LCP (Largest Contentful Paint):** Faster server responses lead to better user experience scores.
- **TTFB (Time to First Byte):** Eliminates database overhead for repeat visitors.

---

## License
The MIT License (MIT). Created with ❤️ by **Shammaa**.
