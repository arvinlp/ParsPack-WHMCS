<?php

/**
 * Parspack Server Management Class
 * ============================================
 * @Author: Arvin Loripour - ViraEcosystem
 * @Date: 2025-10-21 10:21:52
 * Copyright by Arvin Loripour
 * WebSite : http://www.arvinlp.ir
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2025-10-21 10:22:40
 * ============================================
 * مسیر: /modules/servers/parspack/lib/Server.php
 */

namespace Parspack\WHMCS;

use WHMCS\Database\Capsule;

class Server
{
    /**
     * @var Api API Client instance
     */
    private $api;

    /**
     * @var int Service ID
     */
    private $serviceId;

    /**
     * @var string Server ID
     */
    private $serverId;

    /**
     * @var array Server data
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct($apiToken, $serviceId = null)
    {
        $this->api = new Api($apiToken);
        $this->serviceId = $serviceId;

        if ($serviceId) {
            $this->serverId = $this->getServerIdFromDb();
        }
    }

    /**
     * Create new server
     */
    public function create($params)
    {
        // Validate parameters
        $this->validateCreateParams($params);

        // Prepare server data
        $serverData = [
            'plan' => $params['plan'],
            'os' => $params['os'],
            'region' => $params['region'],
            'name' => $params['name'] ?? $this->generateServerName(),
        ];

        // Optional parameters
        if (isset($params['backup'])) {
            $serverData['backup'] = (bool) $params['backup'];
        }

        if (isset($params['ssh_key'])) {
            $serverData['ssh_key'] = $params['ssh_key'];
        }

        if (isset($params['user_data'])) {
            $serverData['user_data'] = $params['user_data'];
        }

        // Create server via API
        $response = $this->api->createServer($serverData);

        if (!$response || !isset($response['data']['id'])) {
            throw new \Exception('Failed to create server: Invalid API response');
        }

        $this->serverId = $response['data']['id'];
        $this->data = $response['data'];

        // Save to database
        $this->saveToDatabase();

        // Wait for server to be ready
        if (isset($params['wait']) && $params['wait']) {
            $this->waitUntilReady();
        }

        return $this->data;
    }

    /**
     * Get server details
     */
    public function get($refresh = false)
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        if ($refresh || empty($this->data)) {
            $response = $this->api->getServer($this->serverId);

            if ($response && isset($response['data'])) {
                $this->data = $response['data'];
                $this->updateDatabase();
            }
        }

        return $this->data;
    }

    /**
     * Update server in database
     */
    public function update($data)
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->updateServer($this->serverId, $data);

        if ($response && isset($response['data'])) {
            $this->data = $response['data'];
            $this->updateDatabase();
        }

        return $this->data;
    }

    /**
     * Delete server
     */
    public function delete()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->deleteServer($this->serverId);

        // Remove from database
        $this->deleteFromDatabase();

        return $response;
    }

    /**
     * Power on server
     */
    public function powerOn()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->powerOnServer($this->serverId);
        $this->logAction('poweron', 'success');

        return $response;
    }

    /**
     * Shutdown server
     */
    public function shutdown()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->shutdownServer($this->serverId);
        $this->logAction('shutdown', 'success');

        return $response;
    }

    /**
     * Reboot server
     */
    public function reboot()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->rebootServer($this->serverId);
        $this->logAction('reboot', 'success');

        return $response;
    }

    /**
     * Reset root password
     */
    public function resetPassword()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->resetPassword($this->serverId);

        if ($response && isset($response['data']['password'])) {
            // Update password in WHMCS
            $this->updateWhmcsPassword($response['data']['password']);
            $this->logAction('reset_password', 'success');
        }

        return $response;
    }

    /**
     * Get server usage
     */
    public function getUsage()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->getServerUsage($this->serverId);

        if ($response && isset($response['data'])) {
            $this->saveUsageToDatabase($response['data']);
        }

        return $response;
    }

    /**
     * Create snapshot
     */
    public function createSnapshot($name = null)
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        if (!$name) {
            $name = 'snapshot-' . date('Y-m-d-H-i-s');
        }

        $response = $this->api->createSnapshot($this->serverId, $name);
        $this->logAction('create_snapshot', 'success', ['name' => $name]);

        return $response;
    }

    /**
     * List snapshots
     */
    public function listSnapshots()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        return $this->api->listSnapshots($this->serverId);
    }

    /**
     * Restore from snapshot
     */
    public function restoreSnapshot($snapshotId)
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->restoreSnapshot($this->serverId, $snapshotId);
        $this->logAction('restore_snapshot', 'success', ['snapshot_id' => $snapshotId]);

        return $response;
    }

    /**
     * Create backup
     */
    public function createBackup()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->createBackup($this->serverId);
        $this->logAction('create_backup', 'success');

        return $response;
    }

    /**
     * List backups
     */
    public function listBackups()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        return $this->api->listBackups($this->serverId);
    }

    /**
     * Resize server
     */
    public function resize($planId)
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->resizeServer($this->serverId, $planId);
        $this->logAction('resize', 'success', ['plan' => $planId]);

        return $response;
    }

    /**
     * Rebuild server
     */
    public function rebuild($osId)
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        $response = $this->api->rebuildServer($this->serverId, $osId);
        $this->logAction('rebuild', 'success', ['os' => $osId]);

        return $response;
    }

    /**
     * Get console URL
     */
    public function getConsoleUrl()
    {
        if (!$this->serverId) {
            throw new \Exception('Server ID not set');
        }

        return $this->api->getConsoleUrl($this->serverId);
    }

    /**
     * Get status
     */
    public function getStatus()
    {
        $data = $this->get(true);
        return $data['status'] ?? 'unknown';
    }

    /**
     * Check if server is running
     */
    public function isRunning()
    {
        return $this->getStatus() === 'running';
    }

    /**
     * Check if server is stopped
     */
    public function isStopped()
    {
        return $this->getStatus() === 'stopped';
    }

    /**
     * Check if server is building
     */
    public function isBuilding()
    {
        return $this->getStatus() === 'building';
    }

    /**
     * Wait until server is ready
     */
    public function waitUntilReady($maxAttempts = 20, $interval = 30)
    {
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            $status = $this->getStatus();

            if ($status === 'running') {
                return true;
            }

            if ($status === 'error') {
                throw new \Exception('Server creation failed');
            }

            sleep($interval);
            $attempts++;
        }

        throw new \Exception('Server creation timeout');
    }

    /**
     * Validate create parameters
     */
    private function validateCreateParams($params)
    {
        $required = ['plan', 'os', 'region'];

        foreach ($required as $field) {
            if (!isset($params[$field]) || empty($params[$field])) {
                throw new \Exception("Missing required parameter: {$field}");
            }
        }
    }

    /**
     * Generate server name
     */
    private function generateServerName()
    {
        $prefix = 'whmcs';
        $random = substr(md5(uniqid()), 0, 8);

        if ($this->serviceId) {
            return "{$prefix}-{$this->serviceId}-{$random}";
        }

        return "{$prefix}-{$random}";
    }

    /**
     * Get server ID from database
     */
    private function getServerIdFromDb()
    {
        try {
            $result = Capsule::table('mod_parspack_servers')
                ->where('service_id', $this->serviceId)
                ->first();

            return $result ? $result->server_id : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Save server to database
     */
    private function saveToDatabase()
    {
        try {
            Capsule::table('mod_parspack_servers')->insert([
                'service_id' => $this->serviceId,
                'server_id' => $this->serverId,
                'server_name' => $this->data['name'] ?? null,
                'server_ip' => $this->data['ip'] ?? null,
                'server_status' => $this->data['status'] ?? 'building',
                'plan_id' => $this->data['plan'] ?? null,
                'os_id' => $this->data['os']['id'] ?? null,
                'region' => $this->data['region'] ?? null,
                'cpu' => $this->data['cpu'] ?? null,
                'ram' => $this->data['ram'] ?? null,
                'disk' => $this->data['disk'] ?? null,
                'backup_enabled' => $this->data['backup'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail
            if (function_exists('logActivity')) {
                logActivity("Parspack: Failed to save server to database: " . $e->getMessage());
            }
        }
    }

    /**
     * Update server in database
     */
    private function updateDatabase()
    {
        try {
            Capsule::table('mod_parspack_servers')
                ->where('service_id', $this->serviceId)
                ->update([
                    'server_name' => $this->data['name'] ?? null,
                    'server_ip' => $this->data['ip'] ?? null,
                    'server_status' => $this->data['status'] ?? null,
                    'cpu' => $this->data['cpu'] ?? null,
                    'ram' => $this->data['ram'] ?? null,
                    'disk' => $this->data['disk'] ?? null,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_sync' => date('Y-m-d H:i:s'),
                ]);
        } catch (\Exception $e) {
            // Ignore errors
        }
    }

    /**
     * Delete server from database
     */
    private function deleteFromDatabase()
    {
        try {
            Capsule::table('mod_parspack_servers')
                ->where('service_id', $this->serviceId)
                ->delete();
        } catch (\Exception $e) {
            // Ignore errors
        }
    }

    /**
     * Update password in WHMCS
     */
    private function updateWhmcsPassword($password)
    {
        try {
            Capsule::table('tblhosting')
                ->where('id', $this->serviceId)
                ->update([
                    'password' => encrypt($password),
                ]);
        } catch (\Exception $e) {
            // Ignore errors
        }
    }

    /**
     * Save usage to database
     */
    private function saveUsageToDatabase($usage)
    {
        try {
            Capsule::table('mod_parspack_usage')->insert([
                'service_id' => $this->serviceId,
                'server_id' => $this->serverId,
                'cpu_usage' => $usage['cpu'] ?? null,
                'ram_usage' => $usage['ram'] ?? null,
                'disk_usage' => $usage['disk'] ?? null,
                'network_in' => $usage['network_in'] ?? null,
                'network_out' => $usage['network_out'] ?? null,
                'recorded_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // Ignore errors
        }
    }

    /**
     * Log action
     */
    private function logAction($action, $status, $data = [])
    {
        try {
            Capsule::table('mod_parspack_logs')->insert([
                'service_id' => $this->serviceId,
                'server_id' => $this->serverId,
                'action' => $action,
                'status' => $status,
                'request_data' => json_encode($data),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            // Ignore errors
        }
    }

    /**
     * Get server ID
     */
    public function getServerId()
    {
        return $this->serverId;
    }
}
