<?php

/**
 * Parspack Module Configuration File
 * 
 * این فایل را در مسیر /modules/servers/parspack/ قرار دهید
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * تنظیمات پیش‌فرض ماژول
 */
return [

    /**
     * آدرس پایه API پارس‌پک
     */
    'api_base_url' => 'https://api.parspack.com/v1',

    /**
     * نسخه API
     */
    'api_version' => 'v1',

    /**
     * تایم‌اوت درخواست‌های API (ثانیه)
     */
    'api_timeout' => 60,

    /**
     * تایم‌اوت اتصال (ثانیه)
     */
    'connection_timeout' => 30,

    /**
     * تعداد تلاش مجدد در صورت خطا
     */
    'retry_attempts' => 3,

    /**
     * فاصله زمانی بین تلاش‌های مجدد (ثانیه)
     */
    'retry_delay' => 5,

    /**
     * فعال‌سازی SSL Verification
     */
    'ssl_verify' => true,

    /**
     * لاگ کردن درخواست‌ها و پاسخ‌های API
     */
    'enable_api_logging' => true,

    /**
     * نمایش جزئیات خطا در محیط توسعه
     */
    'debug_mode' => false,

    /**
     * پیشوند نام سرورها
     */
    'server_name_prefix' => 'whmcs-',

    /**
     * پسوند نام سرورها
     */
    'server_name_suffix' => '',

    /**
     * زمان انتظار برای ساخت سرور (دقیقه)
     */
    'server_build_timeout' => 10,

    /**
     * فاصله بررسی وضعیت سرور (ثانیه)
     */
    'status_check_interval' => 30,

    /**
     * حداکثر تعداد بررسی وضعیت
     */
    'max_status_checks' => 20,

    /**
     * مناطق دیتاسنتر قابل استفاده
     */
    'available_regions' => [
        'ir-thr-ba1' => 'تهران - باقر شهر ۱',
        'ir-thr-mn1' => 'تهران - منیریه ۱',
    ],

    /**
     * سیستم‌عامل‌های پیشنهادی
     */
    'recommended_os' => [
        'ubuntu-22.04' => 'Ubuntu 22.04 LTS',
        'ubuntu-20.04' => 'Ubuntu 20.04 LTS',
        'debian-11' => 'Debian 11',
        'centos-8' => 'CentOS 8',
        'rocky-8' => 'Rocky Linux 8',
        'almalinux-8' => 'AlmaLinux 8',
    ],

    /**
     * پلن‌های پیشنهادی (نمونه)
     */
    'sample_plans' => [
        'c1-m1-d25' => [
            'name' => 'استارتر',
            'cpu' => 1,
            'ram' => 1024,
            'disk' => 25,
            'bandwidth' => 'نامحدود',
        ],
        'c2-m2-d40' => [
            'name' => 'پایه',
            'cpu' => 2,
            'ram' => 2048,
            'disk' => 40,
            'bandwidth' => 'نامحدود',
        ],
        'c2-m4-d60' => [
            'name' => 'پیشرفته',
            'cpu' => 2,
            'ram' => 4096,
            'disk' => 60,
            'bandwidth' => 'نامحدود',
        ],
        'c4-m8-d80' => [
            'name' => 'حرفه‌ای',
            'cpu' => 4,
            'ram' => 8192,
            'disk' => 80,
            'bandwidth' => 'نامحدود',
        ],
    ],

    /**
     * پیام‌های خطا (فارسی)
     */
    'error_messages' => [
        'connection_failed' => 'خطا در اتصال به API پارس‌پک',
        'invalid_token' => 'توکن API نامعتبر است',
        'server_not_found' => 'سرور مورد نظر یافت نشد',
        'insufficient_balance' => 'موجودی حساب کافی نیست',
        'invalid_plan' => 'پلن انتخابی نامعتبر است',
        'invalid_os' => 'سیستم‌عامل انتخابی نامعتبر است',
        'region_unavailable' => 'منطقه انتخابی در دسترس نیست',
        'build_timeout' => 'زمان ساخت سرور به پایان رسید',
        'operation_failed' => 'عملیات با خطا مواجه شد',
    ],

    /**
     * پیام‌های موفقیت (فارسی)
     */
    'success_messages' => [
        'server_created' => 'سرور با موفقیت ایجاد شد',
        'server_deleted' => 'سرور با موفقیت حذف شد',
        'server_started' => 'سرور با موفقیت روشن شد',
        'server_stopped' => 'سرور با موفقیت خاموش شد',
        'server_rebooted' => 'سرور با موفقیت راه‌اندازی مجدد شد',
        'password_reset' => 'رمز عبور با موفقیت بازنشانی شد',
    ],

    /**
     * تنظیمات ایمیل
     */
    'email_settings' => [
        'send_welcome_email' => true,
        'send_status_updates' => true,
        'send_error_notifications' => true,
        'admin_notification_email' => '', // خالی = استفاده از ایمیل پیش‌فرض WHMCS
    ],

    /**
     * قابلیت‌های پیشرفته
     */
    'features' => [
        'auto_backup' => true,
        'monitoring' => true,
        'firewall' => true,
        'snapshots' => true,
        'floating_ip' => true,
        'private_network' => true,
        'load_balancer' => false,
    ],

    /**
     * محدودیت‌های امنیتی
     */
    'security' => [
        'require_ssh_key' => false,
        'disable_password_auth' => false,
        'enable_firewall_by_default' => true,
        'allowed_ports' => [22, 80, 443],
        'blocked_ports' => [25, 465, 587], // SMTP ports
    ],

    /**
     * تنظیمات کش
     */
    'cache' => [
        'enabled' => true,
        'ttl' => 300, // 5 دقیقه
        'driver' => 'file', // file, redis, memcached
    ],

    /**
     * تنظیمات نوتیفیکیشن
     */
    'notifications' => [
        'webhook_url' => '',
        'slack_webhook' => '',
        'telegram_bot_token' => '',
        'telegram_chat_id' => '',
    ],

    /**
     * تنظیمات مانیتورینگ
     */
    'monitoring' => [
        'enabled' => true,
        'check_interval' => 300, // 5 دقیقه
        'alert_on_down' => true,
        'alert_on_high_cpu' => true,
        'cpu_threshold' => 90, // درصد
        'alert_on_high_ram' => true,
        'ram_threshold' => 90, // درصد
        'alert_on_high_disk' => true,
        'disk_threshold' => 85, // درصد
    ],

    /**
     * تنظیمات بکاپ خودکار
     */
    'backup' => [
        'enabled' => false,
        'frequency' => 'weekly', // daily, weekly, monthly
        'retention_days' => 7,
        'auto_delete_old' => true,
    ],

    /**
     * تنظیمات Firewall پیش‌فرض
     */
    'default_firewall_rules' => [
        [
            'protocol' => 'tcp',
            'port' => '22',
            'source' => '0.0.0.0/0',
            'direction' => 'inbound',
            'action' => 'allow',
        ],
        [
            'protocol' => 'tcp',
            'port' => '80',
            'source' => '0.0.0.0/0',
            'direction' => 'inbound',
            'action' => 'allow',
        ],
        [
            'protocol' => 'tcp',
            'port' => '443',
            'source' => '0.0.0.0/0',
            'direction' => 'inbound',
            'action' => 'allow',
        ],
    ],

    /**
     * متادیتای پیش‌فرض برای سرورها
     */
    'default_metadata' => [
        'managed_by' => 'WHMCS',
        'auto_created' => true,
    ],

    /**
     * تنظیمات User Data (Cloud-Init)
     */
    'cloud_init' => [
        'enabled' => false,
        'default_script' => '',
    ],

    /**
     * نقشه وضعیت‌های سرور
     */
    'status_map' => [
        'running' => [
            'label' => 'در حال اجرا',
            'color' => 'success',
            'icon' => 'check-circle',
        ],
        'stopped' => [
            'label' => 'متوقف شده',
            'color' => 'warning',
            'icon' => 'pause-circle',
        ],
        'building' => [
            'label' => 'در حال ساخت',
            'color' => 'info',
            'icon' => 'refresh',
        ],
        'error' => [
            'label' => 'خطا',
            'color' => 'danger',
            'icon' => 'exclamation-circle',
        ],
        'rebooting' => [
            'label' => 'در حال راه‌اندازی',
            'color' => 'info',
            'icon' => 'refresh',
        ],
        'shutting_down' => [
            'label' => 'در حال خاموش شدن',
            'color' => 'warning',
            'icon' => 'power-off',
        ],
    ],

    /**
     * تنظیمات Rate Limiting
     */
    'rate_limiting' => [
        'enabled' => true,
        'max_requests_per_minute' => 60,
        'max_requests_per_hour' => 1000,
    ],

    /**
     * تنظیمات Queue (صف انتظار)
     */
    'queue' => [
        'enabled' => false,
        'driver' => 'database', // database, redis, sync
        'max_jobs' => 100,
        'timeout' => 300,
    ],

    /**
     * تنظیمات گزارش‌گیری
     */
    'reporting' => [
        'enabled' => true,
        'daily_report' => false,
        'weekly_report' => true,
        'monthly_report' => true,
    ],

    /**
     * تنظیمات واحد پول
     */
    'currency' => [
        'default' => 'IRR',
        'symbol' => 'ریال',
    ],

    /**
     * تنظیمات زبان
     */
    'language' => [
        'default' => 'persian',
        'rtl' => true,
    ],

];
