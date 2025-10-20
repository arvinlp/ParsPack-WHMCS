<?php

/**
 * WHMCS Parspack Cloud Server Provisioning Module
 *
 * @copyright Copyright (c) 2025
 * @license https://www.whmcs.com/license/ WHMCS Eula
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Define module related meta data.
 */
function parspack_MetaData()
{
    return array(
        'DisplayName' => 'Parspack Cloud Server',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
        'DefaultNonSSLPort' => '80',
        'DefaultSSLPort' => '443',
        'ServiceSingleSignOnLabel' => 'ورود به پنل مدیریت',
        'AdminSingleSignOnLabel' => 'ورود به پنل ادمین',
    );
}

/**
 * Define product configuration options.
 */
function parspack_ConfigOptions()
{
    return array(
        'plan_id' => array(
            'FriendlyName' => 'شناسه پلن',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'شناسه پلن از پنل پارس‌پک',
        ),
        'os_id' => array(
            'FriendlyName' => 'شناسه سیستم عامل',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => 'شناسه سیستم عامل پیش‌فرض',
        ),
        'region' => array(
            'FriendlyName' => 'منطقه دیتاسنتر',
            'Type' => 'dropdown',
            'Options' => 'ir-thr-ba1,ir-thr-mn1',
            'Default' => 'ir-thr-ba1',
            'Description' => 'انتخاب دیتاسنتر',
        ),
        'backup_enabled' => array(
            'FriendlyName' => 'پشتیبان‌گیری خودکار',
            'Type' => 'yesno',
            'Description' => 'فعال‌سازی پشتیبان‌گیری خودکار',
        ),
    );
}

/**
 * Provision a new instance of a product/service.
 */
function parspack_CreateAccount(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $planId = $params['configoption1'];
        $osId = $params['configoption2'];
        $region = $params['configoption3'];
        $backupEnabled = $params['configoption4'] == 'on' ? true : false;

        // Generate server name
        $serverName = 'server-' . $params['serviceid'] . '-' . substr(md5($params['domain']), 0, 8);

        // Prepare API request
        $postData = array(
            'plan' => $planId,
            'os' => $osId,
            'region' => $region,
            'name' => $serverName,
            'backup' => $backupEnabled,
        );

        // Add SSH key if provided
        if (!empty($params['customfields']['ssh_key'])) {
            $postData['ssh_key'] = $params['customfields']['ssh_key'];
        }

        // Create server
        $response = parspack_ApiCall($apiToken, 'POST', '/cloud/server', $postData);

        if (!$response || !isset($response['data']['id'])) {
            throw new Exception('خطا در ایجاد سرور: پاسخ نامعتبر از API');
        }

        $serverId = $response['data']['id'];
        $serverIp = isset($response['data']['ip']) ? $response['data']['ip'] : '';
        $rootPassword = isset($response['data']['password']) ? $response['data']['password'] : '';

        // Update service custom fields
        $updateData = array(
            'model' => 'service',
            'service_id' => $params['serviceid'],
            'customfield' => array(
                'server_id' => $serverId,
                'ip_address' => $serverIp,
            ),
        );

        // Store server details
        Capsule::table('tblhosting')
            ->where('id', $params['serviceid'])
            ->update([
                'dedicatedip' => $serverIp,
                'username' => 'root',
                'password' => encrypt($rootPassword),
            ]);

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Suspend an instance of a product/service.
 */
function parspack_SuspendAccount(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            throw new Exception('شناسه سرور یافت نشد');
        }

        // Stop the server
        $response = parspack_ApiCall($apiToken, 'POST', "/cloud/server/{$serverId}/shutdown", array());

        if (!$response) {
            throw new Exception('خطا در متوقف کردن سرور');
        }

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Un-suspend instance of a product/service.
 */
function parspack_UnsuspendAccount(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            throw new Exception('شناسه سرور یافت نشد');
        }

        // Start the server
        $response = parspack_ApiCall($apiToken, 'POST', "/cloud/server/{$serverId}/poweron", array());

        if (!$response) {
            throw new Exception('خطا در روشن کردن سرور');
        }

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Terminate instance of a product/service.
 */
function parspack_TerminateAccount(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            throw new Exception('شناسه سرور یافت نشد');
        }

        // Delete the server
        $response = parspack_ApiCall($apiToken, 'DELETE', "/cloud/server/{$serverId}", array());

        if (!$response) {
            throw new Exception('خطا در حذف سرور');
        }

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Test connection with the given server parameters.
 */
function parspack_TestConnection(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];

        $response = parspack_ApiCall($apiToken, 'GET', '/cloud/server', array());

        $success = (bool) $response;
        $errorMsg = $success ? '' : 'اتصال به API پارس‌پک با خطا مواجه شد';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}

/**
 * Admin custom buttons.
 */
function parspack_AdminCustomButtonArray()
{
    return array(
        "راه‌اندازی مجدد" => "reboot",
        "دریافت اطلاعات" => "getinfo",
        "بازیابی رمز عبور" => "resetpassword",
    );
}

/**
 * Client area custom buttons.
 */
function parspack_ClientAreaCustomButtonArray()
{
    return array(
        "راه‌اندازی مجدد" => "reboot",
        "خاموش کردن" => "shutdown",
        "روشن کردن" => "poweron",
    );
}

/**
 * Admin services tab additional fields.
 */
function parspack_AdminServicesTabFields(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            return array();
        }

        $response = parspack_ApiCall($apiToken, 'GET', "/cloud/server/{$serverId}", array());

        if (!$response || !isset($response['data'])) {
            return array();
        }

        $server = $response['data'];

        return array(
            'شناسه سرور' => $server['id'],
            'وضعیت' => parspack_GetStatusLabel($server['status']),
            'آی‌پی' => $server['ip'] ?? 'N/A',
            'CPU' => ($server['cpu'] ?? 0) . ' Core',
            'RAM' => ($server['ram'] ?? 0) . ' MB',
            'دیسک' => ($server['disk'] ?? 0) . ' GB',
            'سیستم عامل' => $server['os']['name'] ?? 'N/A',
            'منطقه' => $server['region'] ?? 'N/A',
        );
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return array();
    }
}

/**
 * Client area output.
 */
function parspack_ClientArea(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            return array(
                'templatefile' => 'templates/clientarea',
                'vars' => array(
                    'error' => 'سرور هنوز ایجاد نشده است',
                ),
            );
        }

        $response = parspack_ApiCall($apiToken, 'GET', "/cloud/server/{$serverId}", array());

        if (!$response || !isset($response['data'])) {
            throw new Exception('خطا در دریافت اطلاعات سرور');
        }

        $server = $response['data'];

        return array(
            'templatefile' => 'templates/clientarea',
            'vars' => array(
                'server' => $server,
                'status' => parspack_GetStatusLabel($server['status']),
            ),
        );
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return array(
            'templatefile' => 'templates/clientarea',
            'vars' => array(
                'error' => $e->getMessage(),
            ),
        );
    }
}

/**
 * Reboot server function.
 */
function parspack_reboot(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            throw new Exception('شناسه سرور یافت نشد');
        }

        $response = parspack_ApiCall($apiToken, 'POST', "/cloud/server/{$serverId}/reboot", array());

        if (!$response) {
            throw new Exception('خطا در راه‌اندازی مجدد سرور');
        }

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Shutdown server function.
 */
function parspack_shutdown(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            throw new Exception('شناسه سرور یافت نشد');
        }

        $response = parspack_ApiCall($apiToken, 'POST', "/cloud/server/{$serverId}/shutdown", array());

        if (!$response) {
            throw new Exception('خطا در خاموش کردن سرور');
        }

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Power on server function.
 */
function parspack_poweron(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            throw new Exception('شناسه سرور یافت نشد');
        }

        $response = parspack_ApiCall($apiToken, 'POST', "/cloud/server/{$serverId}/poweron", array());

        if (!$response) {
            throw new Exception('خطا در روشن کردن سرور');
        }

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Get server info function.
 */
function parspack_getinfo(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            throw new Exception('شناسه سرور یافت نشد');
        }

        $response = parspack_ApiCall($apiToken, 'GET', "/cloud/server/{$serverId}", array());

        if (!$response) {
            throw new Exception('خطا در دریافت اطلاعات سرور');
        }

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Reset password function.
 */
function parspack_resetpassword(array $params)
{
    try {
        $apiToken = $params['serveraccesshash'];
        $serverId = parspack_GetServerId($params['serviceid']);

        if (!$serverId) {
            throw new Exception('شناسه سرور یافت نشد');
        }

        $response = parspack_ApiCall($apiToken, 'POST', "/cloud/server/{$serverId}/reset-password", array());

        if (!$response || !isset($response['data']['password'])) {
            throw new Exception('خطا در بازیابی رمز عبور');
        }

        // Update password in WHMCS
        Capsule::table('tblhosting')
            ->where('id', $params['serviceid'])
            ->update([
                'password' => encrypt($response['data']['password']),
            ]);

        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'parspack',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

/**
 * Helper function to make API calls.
 */
function parspack_ApiCall($apiToken, $method, $endpoint, $data = array())
{
    $baseUrl = 'https://api.parspack.com/v1';
    $url = $baseUrl . $endpoint;

    $ch = curl_init();

    $headers = array(
        'Authorization: Bearer ' . $apiToken,
        'Content-Type: application/json',
        'Accept: application/json',
    );

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

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
        case 'GET':
        default:
            if (!empty($data)) {
                $url .= '?' . http_build_query($data);
                curl_setopt($ch, CURLOPT_URL, $url);
            }
            break;
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception('خطای cURL: ' . $error);
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpCode >= 400) {
        $errorMsg = isset($result['message']) ? $result['message'] : 'خطای ناشناخته';
        throw new Exception('خطای API (' . $httpCode . '): ' . $errorMsg);
    }

    return $result;
}

/**
 * Get server ID from service.
 */
function parspack_GetServerId($serviceId)
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
 * Get status label.
 */
function parspack_GetStatusLabel($status)
{
    $labels = array(
        'running' => 'در حال اجرا',
        'stopped' => 'متوقف شده',
        'building' => 'در حال ساخت',
        'error' => 'خطا',
        'unknown' => 'نامشخص',
    );

    return isset($labels[$status]) ? $labels[$status] : $status;
}
