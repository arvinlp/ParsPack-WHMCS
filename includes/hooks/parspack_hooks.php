<?php

/**
 * WHMCS Parspack Module Hooks
 * ============================================
 * @Author: Arvin Loripour - ViraEcosystem
 * @Date: 2025-10-21 10:21:52
 * Copyright by Arvin Loripour
 * WebSite : http://www.arvinlp.ir
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2025-10-21 10:23:04
 * ============================================
 * این فایل را در مسیر /includes/hooks/ قرار دهید
 */

use WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Hook برای نمایش اطلاعات سرور در Invoice
 */
add_hook('InvoicePaid', 1, function ($vars) {
    $invoiceId = $vars['invoiceid'];

    // دریافت آیتم‌های فاکتور
    $items = Capsule::table('tblinvoiceitems')
        ->where('invoiceid', $invoiceId)
        ->where('type', 'Hosting')
        ->get();

    foreach ($items as $item) {
        if ($item->relid) {
            // بررسی ماژول سرویس
            $service = Capsule::table('tblhosting')
                ->where('id', $item->relid)
                ->first();

            if ($service && $service->server > 0) {
                $server = Capsule::table('tblservers')
                    ->where('id', $service->server)
                    ->first();

                if ($server && $server->type === 'parspack') {
                    // ارسال ایمیل خوش‌آمدگویی با اطلاعات سرور
                    parspack_SendWelcomeEmail($service);
                }
            }
        }
    }
});

/**
 * Hook برای نمایش وضعیت سرور در لیست سرویس‌ها
 */
add_hook('ClientAreaPageProductsServices', 1, function ($vars) {
    $services = Capsule::table('tblhosting as h')
        ->join('tblservers as s', 'h.server', '=', 's.id')
        ->join('tblproducts as p', 'h.packageid', '=', 'p.id')
        ->where('h.userid', $vars['clientsdetails']['userid'])
        ->where('s.type', 'parspack')
        ->select('h.*', 's.accesshash', 's.hostname')
        ->get();

    $serverStatuses = [];

    foreach ($services as $service) {
        try {
            $serverId = parspack_GetServerIdFromService($service->id);
            if ($serverId) {
                $response = parspack_ApiRequest(
                    $service->accesshash,
                    'GET',
                    "/cloud/server/{$serverId}"
                );

                if ($response && isset($response['data'])) {
                    $serverStatuses[$service->id] = [
                        'status' => $response['data']['status'],
                        'ip' => $response['data']['ip'] ?? '',
                        'cpu_usage' => $response['data']['cpu_usage'] ?? 0,
                        'ram_usage' => $response['data']['ram_usage'] ?? 0,
                    ];
                }
            }
        } catch (Exception $e) {
            logActivity("Parspack Hook Error for Service {$service->id}: " . $e->getMessage());
        }
    }

    return ['serverStatuses' => $serverStatuses];
});

/**
 * Hook برای مانیتورینگ خودکار سرورها
 */
add_hook('DailyCronJob', 1, function ($vars) {
    // دریافت تمام سرویس‌های فعال پارس‌پک
    $services = Capsule::table('tblhosting as h')
        ->join('tblservers as s', 'h.server', '=', 's.id')
        ->where('s.type', 'parspack')
        ->whereIn('h.domainstatus', ['Active', 'Suspended'])
        ->select('h.*', 's.accesshash')
        ->get();

    foreach ($services as $service) {
        try {
            $serverId = parspack_GetServerIdFromService($service->id);
            if (!$serverId) continue;

            $response = parspack_ApiRequest(
                $service->accesshash,
                'GET',
                "/cloud/server/{$serverId}"
            );

            if ($response && isset($response['data'])) {
                $serverData = $response['data'];

                // بروزرسانی IP در صورت تغییر
                if (!empty($serverData['ip']) && $service->dedicatedip !== $serverData['ip']) {
                    Capsule::table('tblhosting')
                        ->where('id', $service->id)
                        ->update(['dedicatedip' => $serverData['ip']]);
                }

                // لاگ کردن وضعیت
                if ($serverData['status'] === 'error') {
                    logActivity("Parspack Server Error - Service ID: {$service->id}, Server ID: {$serverId}");
                }
            }
        } catch (Exception $e) {
            logActivity("Parspack Daily Cron Error for Service {$service->id}: " . $e->getMessage());
        }
    }
});

/**
 * Hook برای اضافه کردن منوی سریع در Client Area
 */
add_hook('ClientAreaPrimarySidebar', 1, function ($sidebar) {
    if (!is_null($sidebar->getChild('Service Details Actions'))) {
        $serviceId = $_GET['id'] ?? null;

        if ($serviceId) {
            $service = Capsule::table('tblhosting as h')
                ->join('tblservers as s', 'h.server', '=', 's.id')
                ->where('h.id', $serviceId)
                ->where('s.type', 'parspack')
                ->first();

            if ($service) {
                $sidebar->getChild('Service Details Actions')
                    ->addChild('Parspack Console', [
                        'label' => 'کنسول سرور',
                        'uri' => 'clientarea.php?action=productdetails&id=' . $serviceId . '#console',
                        'icon' => 'fa-terminal',
                        'order' => 10,
                    ]);
            }
        }
    }
});

/**
 * Helper function: ارسال ایمیل خوش‌آمدگویی
 */
function parspack_SendWelcomeEmail($service)
{
    try {
        $client = Capsule::table('tblclients')
            ->where('id', $service->userid)
            ->first();

        if (!$client) return;

        $serverId = parspack_GetServerIdFromService($service->id);
        if (!$serverId) return;

        $product = Capsule::table('tblproducts')
            ->where('id', $service->packageid)
            ->first();

        $emailData = [
            'service_id' => $service->id,
            'product_name' => $product->name ?? 'سرور مجازی',
            'server_ip' => $service->dedicatedip,
            'server_username' => 'root',
            'server_id' => $serverId,
        ];

        // ارسال ایمیل
        sendMessage('Parspack Server Created', $service->id, $emailData);
    } catch (Exception $e) {
        logActivity("Parspack Welcome Email Error: " . $e->getMessage());
    }
}

/**
 * Helper function: دریافت Server ID از سرویس
 */
function parspack_GetServerIdFromService($serviceId)
{
    try {
        $result = Capsule::table('tblcustomfieldsvalues as cfv')
            ->join('tblcustomfields as cf', 'cfv.fieldid', '=', 'cf.id')
            ->where('cfv.relid', $serviceId)
            ->where('cf.fieldname', 'server_id')
            ->first();

        return $result ? $result->value : null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Helper function: درخواست API
 */
function parspack_ApiRequest($apiToken, $method, $endpoint, $data = [])
{
    $baseUrl = 'https://api.parspack.com/v1';
    $url = $baseUrl . $endpoint;

    $ch = curl_init();

    $headers = [
        'Authorization: Bearer ' . $apiToken,
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    } elseif ($method === 'GET' && !empty($data)) {
        $url .= '?' . http_build_query($data);
        curl_setopt($ch, CURLOPT_URL, $url);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        throw new Exception('cURL Error: ' . curl_error($ch));
    }

    curl_close($ch);

    if ($httpCode >= 400) {
        throw new Exception('API Error: HTTP ' . $httpCode);
    }

    return json_decode($response, true);
}

/**
 * Hook برای نمایش آمار مصرف در Client Area
 */
add_hook('ClientAreaPageProductDetails', 1, function ($vars) {
    if (!isset($vars['id'])) return;

    $serviceId = $vars['id'];

    $service = Capsule::table('tblhosting as h')
        ->join('tblservers as s', 'h.server', '=', 's.id')
        ->where('h.id', $serviceId)
        ->where('s.type', 'parspack')
        ->first();

    if (!$service) return;

    try {
        $serverId = parspack_GetServerIdFromService($serviceId);
        if (!$serverId) return;

        $response = parspack_ApiRequest(
            $service->accesshash,
            'GET',
            "/cloud/server/{$serverId}/usage"
        );

        if ($response && isset($response['data'])) {
            return [
                'usage_data' => $response['data'],
                'has_usage' => true,
            ];
        }
    } catch (Exception $e) {
        logActivity("Parspack Usage Error for Service {$serviceId}: " . $e->getMessage());
    }

    return ['has_usage' => false];
});
