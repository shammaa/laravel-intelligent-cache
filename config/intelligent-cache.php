<?php

return [
    /*
     * إيقاف أو تشغيل المكتبة بالكامل
     */
    'enabled' => env('INTELLIGENT_CACHE_ENABLED', true),

    /*
     * المدة الافتراضية للتخزين (بالثواني)
     * 3600 = ساعة واحدة
     */
    'lifetime' => env('INTELLIGENT_CACHE_LIFETIME', 3600),

    /*
     * ميزة التخلص الذكي من الكاش
     * عند تحديث موديل معين، سيتم مسح الكاش المرتبط به تلقائياً
     */
    'auto_invalidation' => [
        'enabled' => true,
        'models' => [
            // مثال: 'App\Models\Article' => ['articles', 'home'],
        ],
    ],

    /*
     * الروابط التي لا نريد تخزينها (مثل لوحة التحكم)
     */
    'exclude' => [
        'admin/*',
        'login',
        'register',
        'api/user',
    ],

    /*
     * إعدادات الـ Headers لتحسين الـ SEO وسرعة المتصفح
     */
    'headers' => [
        'cache_control' => 'public, max-age=3600, must-revalidate',
        'add_cache_status_header' => true, // يضيف X-Cache: HIT أو MISS للرد
    ],

    /*
     * طريقة التخزين
     * يفضل استخدام file أو redis
     */
    'driver' => env('INTELLIGENT_CACHE_DRIVER', 'file'),
];
