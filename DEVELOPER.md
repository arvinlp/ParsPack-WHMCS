# راهنمای توسعه‌دهنده - ماژول Parspack WHMCS

این سند راهنمای کاملی برای توسعه‌دهندگانی است که می‌خواهند ماژول را سفارشی‌سازی یا گسترش دهند.

## ساختار فایل‌ها

```
modules/servers/parspack/
├── parspack.php              # فایل اصلی ماژول
├── config.php                # تنظیمات ماژول
├── templates/
│   └── clientarea.tpl        # قالب ناحیه کاربری
├── lib/
│   ├── Api.php              # کلاس API
│   ├── Server.php           # کلاس مدیریت سرور
│   └── Helper.php           # توابع کمکی
└── lang/
    ├── persian.php          # ترجمه فارسی
    └── english.php          # ترجمه انگلیسی

includes/hooks/
└── parspack_hooks.php        # Hook های ماژول
```

## معماری ماژول

### 1. توابع اصلی WHMCS

ماژول از توابع استاندارد WHMCS برای provisioning استفاده می‌کند:

```php
// متادیتا
function parspack_MetaData()

// تنظیمات محصول
function parspack_ConfigOptions()

// عملیات CRUD
function parspack_CreateAccount(array $params)
function parspack_SuspendAccount(array $params)
function parspack_UnsuspendAccount(array $params)
function parspack_TerminateAccount(array $params)

// تست اتصال
function parspack_TestConnection(array $params)

// دکمه‌های سفارشی
function parspack_AdminCustomButtonArray()
function parspack_ClientAreaCustomButtonArray()

// نمایش اطلاعات
function parspack_AdminServicesTabFields(array $params)
function parspack_ClientArea(array $params)
```

### 2. پارامترهای ورودی

هر تابع یک آرایه `$params` دریافت می‌کند که شامل:

```php
[
    'serviceid' => 123,                    // شناسه سرویس
    'userid' => 456,                       // شناسه مشتری
    'domain' => 'example.com',             // دامنه سرویس
    'serveraccesshash' => 'API_TOKEN',     // توکن API
    'configoption1' => 'plan_id',          // پلن
    'configoption2' => 'os_id',            // سیستم عامل
    'configoption3' => 'region',           // منطقه
    'configoption4' => 'backup_enabled',   // بکاپ
    'customfields' => [...],               // فیلدهای سفارشی
]
```

## توسعه کلاس API (پیشنهادی)

برای مدیریت بهتر API، می‌توانید یک کلاس جداگانه ایجاد کنید:

```php
<?php
// lib/Api.php

namespace Parspack\WHMCS;

class Api
{
    private $token;
    private $baseUrl = 'https://api.parspack.com/v1';
    
    public function __construct($token)
    {
        $this->token = $token;
    }
    
    public function createServer($data)
    {
        return $this->request('POST', '/cloud/server', $data);
    }
    
    public function getServer($serverId)
    {
        return $this->request('GET', "/cloud/server/{$serverId}");
    }
    
    public function deleteServer($serverId)
    {
        return $this->request('DELETE', "/cloud/server/{$serverId}");
    }
    
    public function rebootServer($serverId)
    {
        return $this->request('POST', "/cloud/server/{$serverId}/reboot");
    }
    
    public function shutdownServer($serverId)
    {
        return $this->request('POST', "/cloud/server/{$serverId}/shutdown");
    }
    
    public function powerOnServer($serverId)
    {
        return $this->request('POST', "/cloud/server/{$serverId}/poweron");
    }
    
    private function request($method, $endpoint, $data = [])
    {
        $ch = curl_init();
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 60,
        ]);
        
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (!empty($data)) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception("cURL Error: {$error}");
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $message = $result['message'] ?? 'Unknown error';
            throw new \Exception("API Error ({$httpCode}): {$message}");
        }
        
        return $result;
    }
}
```

## افزودن قابلیت‌های جدید

### 1. افزودن دکمه سفارشی

```php
function parspack_AdminCustomButtonArray()
{
    return array(
        "راه‌اندازی مجدد" => "reboot",
        "دریافت اطلاعات" => "getinfo",
        "ایجاد اسنپ‌شات" => "createsnapshot",  // جدید
    );
}

function parspack_createsnapshot(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);
        
        $postData = [
            'name' => 'snapshot-' . date('Y-m-d-H-i-s'),
        ];
        
        $response = parspack_ApiCall(
            $apiToken, 
            'POST', 
            "/cloud/server/{$serverId}/snapshot",
            $postData
        );
        
        return 'success';
        
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
```

### 2. افزودن فیلد تنظیمات

```php
function parspack_ConfigOptions()
{
    return array(
        // ... فیلدهای موجود
        
        'auto_backup_retention' => array(
            'FriendlyName' => 'مدت نگهداری بکاپ',
            'Type' => 'dropdown',
            'Options' => '7,14,30',
            'Default' => '7',
            'Description' => 'تعداد روز نگهداری بکاپ',
        ),
    );
}
```

### 3. ایجاد Hook سفارشی

```php
// در فایل includes/hooks/parspack_custom.php

add_hook('AfterModuleCreate', 1, function($vars) {
    if ($vars['params']['moduletype'] === 'parspack') {
        // اجرای عملیات پس از ساخت سرور
        $serviceId = $vars['params']['serviceid'];
        
        // مثال: ارسال نوتیفیکیشن به Slack
        $webhookUrl = 'YOUR_SLACK_WEBHOOK_URL';
        $message = "سرور جدید ایجاد شد - Service ID: {$serviceId}";
        
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['text' => $message]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
});
```

## Debugging و Troubleshooting

### فعال‌سازی Debug Mode

```php
// در parspack.php
define('PARSPACK_DEBUG', true);

function parspack_ApiCall($apiToken, $method, $endpoint, $data = array())
{
    if (defined('PARSPACK_DEBUG') && PARSPACK_DEBUG) {
        logModuleCall(
            'parspack',
            'API Request',
            [
                'method' => $method,
                'endpoint' => $endpoint,
                'data' => $data,
            ],
            '',
            ''
        );
    }
    
    // ... باقی کد
    
    if (defined('PARSPACK_DEBUG') && PARSPACK_DEBUG) {
        logModuleCall(
            'parspack',
            'API Response',
            $response,
            $httpCode,
            ''
        );
    }
}
```

### بررسی لاگ‌ها

```php
// دریافت لاگ‌های ماژول از دیتابیس
$logs = Capsule::table('tblmodulelog')
    ->where('module', 'parspack')
    ->orderBy('date', 'desc')
    ->limit(50)
    ->get();

foreach ($logs as $log) {
    echo "{$log->date} - {$log->action}\n";
    echo "Request: {$log->request}\n";
    echo "Response: {$log->response}\n\n";
}
```

## تست واحد (Unit Testing)

```php
// tests/ParspackApiTest.php

use PHPUnit\Framework\TestCase;

class ParspackApiTest extends TestCase
{
    private $api;
    
    protected function setUp(): void
    {
        $this->api = new \Parspack\WHMCS\Api('test_token');
    }
    
    public function testCreateServer()
    {
        $data = [
            'plan' => 'c2-m2-d40',
            'os' => 'ubuntu-22.04',
            'region' => 'ir-thr-ba1',
            'name' => 'test-server',
        ];
        
        $result = $this->api->createServer($data);
        
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('id', $result['data']);
    }
}
```

## بهترین روش‌ها (Best Practices)

### 1. مدیریت خطا

```php
try {
    // عملیات API
    $response = parspack_ApiCall(...);
    
} catch (Exception $e) {
    // لاگ کردن خطا
    logModuleCall(
        'parspack',
        __FUNCTION__,
        $params,
        $e->getMessage(),
        $e->getTraceAsString()
    );
    
    // ارسال نوتیفیکیشن به ادمین
    if ($e->getCode() >= 500) {
        sendAdminNotification(
            'Parspack API Error',
            "API Error: {$e->getMessage()}"
        );
    }
    
    // بازگشت پیام خطا
    return $e->getMessage();
}
```

### 2. استفاده از Cache

```php
function parspack_GetServerInfo($serverId, $useCache = true)
{
    $cacheKey = "parspack_server_{$serverId}";
    $cacheTtl = 300; // 5 دقیقه
    
    if ($useCache) {
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }
    }
    
    $response = parspack_ApiCall(...);
    
    if ($useCache && $response) {
        Cache::put($cacheKey, $response, $cacheTtl);
    }
    
    return $response;
}
```

### 3. Validation ورودی‌ها

```php
function parspack_ValidateParams($params)
{
    $errors = [];
    
    if (empty($params['configoption1'])) {
        $errors[] = 'شناسه پلن الزامی است';
    }
    
    if (empty($params['configoption2'])) {
        $errors[] = 'شناسه سیستم‌عامل الزامی است';
    }
    
    if (!in_array($params['configoption3'], ['ir-thr-ba1', 'ir-thr-mn1'])) {
        $errors[] = 'منطقه نامعتبر است';
    }
    
    if (!empty($errors)) {
        throw new Exception(implode(', ', $errors));
    }
    
    return true;
}
```

## API Endpoints پارس‌پک

### لیست کامل Endpoints

```
GET    /cloud/server              # لیست سرورها
POST   /cloud/server              # ایجاد سرور
GET    /cloud/server/{id}         # جزئیات سرور
DELETE /cloud/server/{id}         # حذف سرور
POST   /cloud/server/{id}/poweron # روشن کردن
POST   /cloud/server/{id}/shutdown # خاموش کردن
POST   /cloud/server/{id}/reboot  # راه‌اندازی مجدد
POST   /cloud/server/{id}/reset-password # بازنشانی رمز
GET    /cloud/server/{id}/usage   # آمار مصرف
POST   /cloud/server/{id}/snapshot # اسنپ‌شات
GET    /cloud/plan                # لیست پلن‌ها
GET    /cloud/os                  # لیست سیستم‌عامل‌ها
GET    /cloud/region              # لیست مناطق
```

## مشارکت در توسعه

برای مشارکت در توسعه ماژول:

1. Fork کردن پروژه
2. ایجاد Branch جدید
3. اعمال تغییرات
4. ارسال Pull Request

## پشتیبانی

برای سوالات فنی:
- مستندات: https://docs.parspack.com
- GitHub Issues: [لینک ریپوزیتوری]
- Email: developer@parspack.com

## لایسنس

این ماژول تحت لایسنس MIT منتشر شده است.