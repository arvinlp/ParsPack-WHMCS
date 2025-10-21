<?php

/**
 * Parspack Module Autoloader
 * ============================================
 * @Author: Arvin Loripour - ViraEcosystem
 * @Date: 2025-10-21 10:21:52
 * Copyright by Arvin Loripour
 * WebSite : http://www.arvinlp.ir
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2025-10-21 10:24:30
 * ============================================
 * مسیر: /modules/servers/parspack/autoload.php
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * PSR-4 Autoloader for Parspack module
 */
spl_autoload_register(function ($class) {
    // Namespace prefix
    $prefix = 'Parspack\\WHMCS\\';

    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/lib/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relativeClass = substr($class, $len);

    // Replace namespace separators with directory separators
    // and append with .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Load configuration
 */
if (file_exists(__DIR__ . '/config.php')) {
    $parspackConfig = require __DIR__ . '/config.php';

    // Make config globally available
    if (!defined('PARSPACK_CONFIG')) {
        define('PARSPACK_CONFIG', $parspackConfig);
    }
}

/**
 * Helper function to get config value
 */
if (!function_exists('parspack_config')) {
    function parspack_config($key, $default = null)
    {
        if (defined('PARSPACK_CONFIG')) {
            $config = PARSPACK_CONFIG;

            // Support dot notation (e.g., 'api.base_url')
            $keys = explode('.', $key);
            foreach ($keys as $k) {
                if (isset($config[$k])) {
                    $config = $config[$k];
                } else {
                    return $default;
                }
            }

            return $config;
        }

        return $default;
    }
}

/**
 * Helper function to get language string
 */
if (!function_exists('parspack_lang')) {
    function parspack_lang($key, $replacements = [])
    {
        global $_LANG;

        // Try to get from loaded language file
        if (isset($_LANG['parspack'][$key])) {
            $string = $_LANG['parspack'][$key];
        } else {
            // Fallback to key
            $string = $key;
        }

        // Replace placeholders
        foreach ($replacements as $search => $replace) {
            $string = str_replace('{' . $search . '}', $replace, $string);
        }

        return $string;
    }
}

/**
 * Initialize module
 */
if (!function_exists('parspack_init')) {
    function parspack_init()
    {
        // Check if tables exist
        if (!\Parspack\WHMCS\Helper::tableExists('mod_parspack_servers')) {
            // Log warning
            if (function_exists('logActivity')) {
                logActivity('Parspack: Database tables not found. Please run install.sql');
            }
        }

        // Load language file based on current language
        $currentLang = isset($_SESSION['Language']) ? $_SESSION['Language'] : 'english';
        $langFile = __DIR__ . '/lang/' . strtolower($currentLang) . '.php';

        if (!file_exists($langFile)) {
            $langFile = __DIR__ . '/lang/english.php';
        }

        if (file_exists($langFile)) {
            $langStrings = require $langFile;
            if (is_array($langStrings)) {
                global $_LANG;
                if (!isset($_LANG['parspack'])) {
                    $_LANG['parspack'] = [];
                }
                $_LANG['parspack'] = array_merge($_LANG['parspack'], $langStrings);
            }
        }

        return true;
    }
}

// Auto-initialize on load
parspack_init();
