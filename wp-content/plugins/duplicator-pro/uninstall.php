<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * Maintain PHP 5.2 compatibility, don't use namespace and don't include Duplicator Libs
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


/**
 * Uninstall class
 * Maintain PHP 5.2 compatibility, don't use namespace and don't include Duplicator Libs.
 *
 * This is a standalone class.
 */
class DuplicatorProUninstall // phpcs:ignore
{
    const ENTITIES_TABLE_NAME           = 'duplicator_entities';
    const PACKAGES_TABLE_NAME           = 'duplicator_backups';
    const OLD_ENTITIES_TABLE_NAME       = 'duplicator_pro_entities';
    const OLD_PACKAGES_TABLE_NAME       = 'duplicator_pro_packages';
    const VERSION_OPTION_KEY            = 'duplicator_pro_plugin_version';
    const UNINSTALL_PACKAGE_OPTION_KEY  = 'duplicator_pro_uninstall_package';
    const UNINSTALL_SETTINGS_OPTION_KEY = 'duplicator_pro_uninstall_settings';

    /**
     * Uninstall plugin
     *
     * @return void
     */
    public static function uninstall()
    {
        try {
            do_action('duplicator_unistall');
            self::removePackages();
            self::removeSettings();
            self::removePluginVersion();
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('Duplicator PRO uninstall clenan Backups error: ' . $e->getMessage());
            }
            // Prevent error on uninstall
        } catch (Error $e) {
            if (function_exists('error_log')) {
                error_log('Duplicator PRO uninstall clenan Backups error: ' . $e->getMessage());
            }
            // Prevent error on uninstall
        }
    }

    /**
     * Remove plugin option version
     *
     * @return void
     */
    protected static function removePluginVersion()
    {
        delete_option(self::VERSION_OPTION_KEY);
    }

    /**
     * Return duplicator PRO backup path
     *
     * @return string
     */
    protected static function getBackupPath()
    {
        return trailingslashit(wp_normalize_path((string) realpath(WP_CONTENT_DIR))) . 'backups-dup-pro';
    }

    /**
     * Remove all Backups
     *
     * @return void
     */
    protected static function removePackages()
    {
        if (get_option(self::UNINSTALL_PACKAGE_OPTION_KEY) != true) {
            return;
        }

        try {
            $tableName = $GLOBALS['wpdb']->base_prefix . self::PACKAGES_TABLE_NAME;
            $GLOBALS['wpdb']->query('DROP TABLE IF EXISTS ' . $tableName);
            $tableName = $GLOBALS['wpdb']->base_prefix . self::OLD_PACKAGES_TABLE_NAME;
            $GLOBALS['wpdb']->query('DROP TABLE IF EXISTS ' . $tableName);

            $ssdir = self::getBackupPath();

            // Sanity check for strange setup
            $check = glob("{$ssdir}/wp-config.php");

            if (is_array($check) && count($check) == 0) {
                if (!class_exists('WP_Filesystem_Direct')) {
                    include_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php');
                }
                if (!class_exists('WP_Filesystem_Base')) {
                    include_once(ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php');
                }
                $fsystem = new WP_Filesystem_Direct(true);
                $fsystem->rmdir($ssdir, true);
            }
        } catch (Exception $e) {
            if (function_exists('error_log')) {
                error_log('Duplicator PRO uninstall clenan Backups error: ' . $e->getMessage());
            }
            // Prevent error on remove Backups
        } catch (Error $e) {
            if (function_exists('error_log')) {
                error_log('Duplicator PRO uninstall clenan Backups error: ' . $e->getMessage());
            }
            // Prevent error on remove Backups
        }
    }

    /**
     * Remove plugins settings
     *
     * @return void
     */
    protected static function removeSettings()
    {
        if (get_option(self::UNINSTALL_SETTINGS_OPTION_KEY) != true) {
            return;
        }

        $tableName = $GLOBALS['wpdb']->base_prefix . self::ENTITIES_TABLE_NAME;
        $GLOBALS['wpdb']->query('DROP TABLE IF EXISTS ' . $tableName);
        $tableName = $GLOBALS['wpdb']->base_prefix . self::OLD_ENTITIES_TABLE_NAME;
        $GLOBALS['wpdb']->query('DROP TABLE IF EXISTS ' . $tableName);

        self::removeAllCapabilities();
        self::deleteExpire();
        self::deleteTransients();
        self::deleteUserMetaKeys();
        self::deleteOptions(); // Keep delete options function as last to make sure other cleanup functions do not need the options
        self::cleanWpConfig();
    }

    /**
     * Delete all users meta key
     *
     * @return void
     */
    private static function deleteUserMetaKeys()
    {
        /** @var wpdb */
        global $wpdb;

        $wpdb->query("DELETE FROM " . $wpdb->usermeta . " WHERE meta_key REGEXP '^duplicator_pro_' OR meta_key = 'duplicator_user_ui_option'");
    }

    /**
     * Delete all options
     *
     * @return void
     */
    protected static function deleteOptions()
    {
        $optionsTableName = $GLOBALS['wpdb']->base_prefix . "options";
        $dupOptionNames   = $GLOBALS['wpdb']->get_col("SELECT `option_name` FROM `{$optionsTableName}` WHERE `option_name` REGEXP '^duplicator_pro_'");

        foreach ($dupOptionNames as $dupOptionName) {
            delete_option($dupOptionName);
        }
    }

    /**
     * Delete all expire options
     *
     * @return void
     */
    protected static function deleteExpire()
    {
        $optionsTableName = $GLOBALS['wpdb']->base_prefix . "options";
        $dupOptionNames   = $GLOBALS['wpdb']->get_col("SELECT `option_name` FROM `{$optionsTableName}` WHERE `option_name` REGEXP '^duplicator_expire_'");

        foreach ($dupOptionNames as $dupOptionName) {
            delete_option($dupOptionName);
        }
    }

    /**
     * Delete all transients
     *
     * @return void
     */
    protected static function deleteTransients()
    {
        $tables = [
            [
                'name' => 'options',
                'key'  => 'option_name',
            ],
        ];

        if (is_multisite()) {
            $tables[] = [
                'name' => 'sitemeta',
                'key'  => 'meta_key',
            ];
        }

        foreach ($tables as $table) {
            $mainName = $table['name'];
            $tableKey = $table['key'];

            $tableName         = $GLOBALS['wpdb']->base_prefix . $mainName;
            $dupTransientNames = $GLOBALS['wpdb']->get_col(
                "SELECT `{$tableKey}` FROM `{$tableName}` WHERE `{$tableKey}` REGEXP '^(_site)?_transient_duplicator_'"
            );

            foreach ($dupTransientNames as $dupTransientName) {
                if (strpos($dupTransientName, '_site') === 0) {
                    delete_site_transient(str_replace("_site_transient_", "", $dupTransientName));
                } else {
                    delete_transient(str_replace("_transient_", "", $dupTransientName));
                }
            }
        }
    }

    /**
     * wp-config.php cleanup
     *
     * @return bool false if wp-config.php not found
     */
    protected static function cleanWpConfig()
    {
        if (($wpConfigFile = self::getWPConfigPath()) === false) {
            return false;
        }

        if (($content = file_get_contents($wpConfigFile)) === false) {
            return false;
        }

        $content = preg_replace('/^.*define.+[\'"]DUPLICATOR_AUTH_KEY[\'"].*$/m', '', $content);

        return (file_put_contents($wpConfigFile, $content) !== false);
    }

    /**
     * Return wp-config path or false if not found
     *
     * @return false|string
     */
    protected static function getWPConfigPath()
    {
        static $configPath = null;
        if (is_null($configPath)) {
            $absPath   = trailingslashit(ABSPATH);
            $absParent = dirname($absPath) . '/';

            if (file_exists($absPath . 'wp-config.php')) {
                $configPath = $absPath . 'wp-config.php';
            } elseif (@file_exists($absParent . 'wp-config.php') && !@file_exists($absParent . 'wp-settings.php')) {
                $configPath = $absParent . 'wp-config.php';
            } else {
                $configPath = false;
            }
        }
        return $configPath;
    }

    /**
     * Remove all capabilities
     *
     * @return void
     */
    protected static function removeAllCapabilities()
    {
        if (($capabilities = get_option('duplicator_pro_capabilities')) == false) {
            return;
        }

        foreach ($capabilities as $cap => $data) {
            foreach ($data['roles'] as $role) {
                $role = get_role($role);
                if ($role) {
                    $role->remove_cap($cap);
                }
            }
            foreach ($data['users'] as $user) {
                $user = get_user_by('id', $user);
                if ($user) {
                    $user->remove_cap($cap);
                }
            }
        }
    }
}

DuplicatorProUninstall::uninstall();
