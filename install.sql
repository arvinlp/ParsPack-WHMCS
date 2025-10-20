-- SQL Installation Script for Parspack WHMCS Module
-- این فایل را در phpMyAdmin اجرا کنید یا از طریق WHMCS Hook
-- جدول ذخیره اطلاعات تکمیلی سرورها
CREATE TABLE IF NOT EXISTS `mod_parspack_servers` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_id` int(10) UNSIGNED NOT NULL,
    `server_id` varchar(100) NOT NULL,
    `server_name` varchar(255) DEFAULT NULL,
    `server_ip` varchar(45) DEFAULT NULL,
    `server_status` varchar(50) DEFAULT NULL,
    `plan_id` varchar(100) DEFAULT NULL,
    `os_id` varchar(100) DEFAULT NULL,
    `region` varchar(50) DEFAULT NULL,
    `cpu` int(5) DEFAULT NULL,
    `ram` int(10) DEFAULT NULL,
    `disk` int(10) DEFAULT NULL,
    `backup_enabled` tinyint(1) DEFAULT 0,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `last_sync` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `service_id` (`service_id`),
    UNIQUE KEY `server_id` (`server_id`),
    KEY `server_status` (`server_status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول لاگ عملیات‌های سرور
CREATE TABLE IF NOT EXISTS `mod_parspack_logs` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_id` int(10) UNSIGNED NOT NULL,
    `server_id` varchar(100) DEFAULT NULL,
    `action` varchar(100) NOT NULL,
    `status` enum('pending', 'success', 'failed') DEFAULT 'pending',
    `request_data` text DEFAULT NULL,
    `response_data` text DEFAULT NULL,
    `error_message` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `service_id` (`service_id`),
    KEY `server_id` (`server_id`),
    KEY `action` (`action`),
    KEY `status` (`status`),
    KEY `created_at` (`created_at`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول ذخیره اطلاعات بکاپ
CREATE TABLE IF NOT EXISTS `mod_parspack_backups` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_id` int(10) UNSIGNED NOT NULL,
    `server_id` varchar(100) NOT NULL,
    `backup_id` varchar(100) NOT NULL,
    `backup_name` varchar(255) DEFAULT NULL,
    `backup_size` bigint(20) DEFAULT NULL,
    `backup_status` varchar(50) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `expires_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `backup_id` (`backup_id`),
    KEY `service_id` (`service_id`),
    KEY `server_id` (`server_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول ذخیره Snapshots
CREATE TABLE IF NOT EXISTS `mod_parspack_snapshots` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_id` int(10) UNSIGNED NOT NULL,
    `server_id` varchar(100) NOT NULL,
    `snapshot_id` varchar(100) NOT NULL,
    `snapshot_name` varchar(255) DEFAULT NULL,
    `snapshot_size` bigint(20) DEFAULT NULL,
    `snapshot_status` varchar(50) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `snapshot_id` (`snapshot_id`),
    KEY `service_id` (`service_id`),
    KEY `server_id` (`server_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول ذخیره آمار مصرف
CREATE TABLE IF NOT EXISTS `mod_parspack_usage` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_id` int(10) UNSIGNED NOT NULL,
    `server_id` varchar(100) NOT NULL,
    `cpu_usage` decimal(5, 2) DEFAULT NULL,
    `ram_usage` decimal(5, 2) DEFAULT NULL,
    `disk_usage` decimal(5, 2) DEFAULT NULL,
    `network_in` bigint(20) DEFAULT NULL,
    `network_out` bigint(20) DEFAULT NULL,
    `recorded_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `service_id` (`service_id`),
    KEY `server_id` (`server_id`),
    KEY `recorded_at` (`recorded_at`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول تنظیمات Firewall
CREATE TABLE IF NOT EXISTS `mod_parspack_firewall_rules` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_id` int(10) UNSIGNED NOT NULL,
    `server_id` varchar(100) NOT NULL,
    `rule_id` varchar(100) DEFAULT NULL,
    `protocol` varchar(10) NOT NULL,
    `port` varchar(20) NOT NULL,
    `source` varchar(100) NOT NULL,
    `direction` enum('inbound', 'outbound') DEFAULT 'inbound',
    `action` enum('allow', 'deny') DEFAULT 'allow',
    `description` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `service_id` (`service_id`),
    KEY `server_id` (`server_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول IP های شناور (Floating IPs)
CREATE TABLE IF NOT EXISTS `mod_parspack_floating_ips` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_id` int(10) UNSIGNED NOT NULL,
    `server_id` varchar(100) DEFAULT NULL,
    `ip_id` varchar(100) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `region` varchar(50) DEFAULT NULL,
    `status` varchar(50) DEFAULT NULL,
    `assigned_at` datetime DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ip_id` (`ip_id`),
    UNIQUE KEY `ip_address` (`ip_address`),
    KEY `service_id` (`service_id`),
    KEY `server_id` (`server_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول شبکه‌های خصوصی
CREATE TABLE IF NOT EXISTS `mod_parspack_private_networks` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `network_id` varchar(100) NOT NULL,
    `network_name` varchar(255) NOT NULL,
    `cidr` varchar(50) NOT NULL,
    `region` varchar(50) DEFAULT NULL,
    `created_by` int(10) UNSIGNED NOT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `network_id` (`network_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول عضویت سرورها در شبکه‌های خصوصی
CREATE TABLE IF NOT EXISTS `mod_parspack_network_members` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `network_id` varchar(100) NOT NULL,
    `service_id` int(10) UNSIGNED NOT NULL,
    `server_id` varchar(100) NOT NULL,
    `private_ip` varchar(45) DEFAULT NULL,
    `joined_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `network_id` (`network_id`),
    KEY `service_id` (`service_id`),
    KEY `server_id` (`server_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول ذخیره کلیدهای SSH
CREATE TABLE IF NOT EXISTS `mod_parspack_ssh_keys` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` int(10) UNSIGNED NOT NULL,
    `key_id` varchar(100) DEFAULT NULL,
    `key_name` varchar(255) NOT NULL,
    `public_key` text NOT NULL,
    `fingerprint` varchar(255) DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `key_id` (`key_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول تنظیمات ماژول
CREATE TABLE IF NOT EXISTS `mod_parspack_settings` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL,
    `setting_value` text DEFAULT NULL,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- جدول صف انتظار (Queue)
CREATE TABLE IF NOT EXISTS `mod_parspack_queue` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `job_type` varchar(100) NOT NULL,
    `service_id` int(10) UNSIGNED NOT NULL,
    `payload` text NOT NULL,
    `status` enum('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    `attempts` int(3) DEFAULT 0,
    `max_attempts` int(3) DEFAULT 3,
    `error_message` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `processed_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `status` (`status`),
    KEY `job_type` (`job_type`),
    KEY `service_id` (`service_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO
    `mod_parspack_settings` (`setting_key`, `setting_value`)
VALUES
    ('module_version', '1.0.0'),
    ('last_sync', NULL),
    ('enable_monitoring', '1'),
    ('enable_auto_backup', '0'),
    ('notification_email', ''),
    ('debug_mode', '0') ON DUPLICATE KEY
UPDATE
    `setting_key` = `setting_key`;

-- Create indexes for better performance
CREATE INDEX idx_service_status ON mod_parspack_servers(service_id, server_status);

CREATE INDEX idx_log_date ON mod_parspack_logs(created_at DESC);

CREATE INDEX idx_usage_date ON mod_parspack_usage(recorded_at DESC);

-- تریگر برای بروزرسانی خودکار updated_at
DELIMITER $ $ CREATE TRIGGER `parspack_servers_update` BEFORE
UPDATE
    ON `mod_parspack_servers` FOR EACH ROW BEGIN
SET
    NEW.updated_at = NOW();

END $ $ DELIMITER;

-- Stored Procedure برای پاکسازی لاگ‌های قدیمی
DELIMITER $ $ CREATE PROCEDURE `parspack_cleanup_old_logs`(IN days INT) BEGIN
DELETE FROM
    mod_parspack_logs
WHERE
    created_at < DATE_SUB(NOW(), INTERVAL days DAY);

DELETE FROM
    mod_parspack_usage
WHERE
    recorded_at < DATE_SUB(NOW(), INTERVAL days DAY);

END $ $ DELIMITER;

-- Stored Procedure برای گزارش آمار
DELIMITER $ $ CREATE PROCEDURE `parspack_get_stats`() BEGIN
SELECT
    COUNT(*) as total_servers,
    SUM(
        CASE
            WHEN server_status = 'running' THEN 1
            ELSE 0
        END
    ) as running_servers,
    SUM(
        CASE
            WHEN server_status = 'stopped' THEN 1
            ELSE 0
        END
    ) as stopped_servers,
    SUM(
        CASE
            WHEN server_status = 'error' THEN 1
            ELSE 0
        END
    ) as error_servers,
    SUM(cpu) as total_cpu,
    SUM(ram) as total_ram,
    SUM(disk) as total_disk
FROM
    mod_parspack_servers;

END $ $ DELIMITER;

-- View برای نمایش اطلاعات کامل سرورها
CREATE
OR REPLACE VIEW `view_parspack_servers_full` AS
SELECT
    s.id,
    s.service_id,
    s.server_id,
    s.server_name,
    s.server_ip,
    s.server_status,
    s.plan_id,
    s.os_id,
    s.region,
    s.cpu,
    s.ram,
    s.disk,
    s.backup_enabled,
    s.created_at,
    s.updated_at,
    h.userid,
    h.domain,
    h.domainstatus,
    c.firstname,
    c.lastname,
    c.email
FROM
    mod_parspack_servers s
    LEFT JOIN tblhosting h ON s.service_id = h.id
    LEFT JOIN tblclients c ON h.userid = c.id;