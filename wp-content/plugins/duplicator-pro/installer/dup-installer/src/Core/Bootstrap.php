<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Installer\Core;

use Duplicator\Installer\Core\Addons\InstAddonsManager;
use Duplicator\Installer\Core\Deploy\ServerConfigs;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\InstallerOrigFileMng;
use Duplicator\Installer\Utils\InstDescMng;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Utils\Log\LogHandler;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapUtil;
use DUPX_U;

/**
 * Class that collects the functions of initial duplicator Bootstrap
 */
class Bootstrap
{
    const DESCRIPTORS_PREFIX  = 'dup_descriptors_';
    const MINIMUM_PHP_VERSION = '5.6.20';

    /**
     * this variable becomes false after the installer is initialized by skipping the shutdown function defined in the boot class
     *
     * @var bool
     */
    private static $shutdownFunctionEnaled = true;

    /**
     *
     * @var int
     */
    public static $dupInitFolderParentLevel = 1;

    /**
     * Init installer
     *
     * @param integer $folderParentLevel num folder parents of home path
     *
     * @return void
     */
    public static function init($folderParentLevel = 1)
    {
        self::setHTTPHeaders();
        self::$dupInitFolderParentLevel = max(1, (int) $folderParentLevel);
        self::phpVersionCheck();

        $GLOBALS['DUPX_ENFORCE_PHP_INI'] = false;

        // INIT ERROR LOG FILE (called before evrithing)
        if (function_exists('register_shutdown_function')) {
            register_shutdown_function(array(__CLASS__, 'bootShutdown'));
        }
        if (self::initPhpErrorLog(false) === false) {
            // Enable this only for debugging. Generate a log too alarmist.
            SnapUtil::errorLog('DUPLICATOR CAN\'T CHANGE THE PATH OF PHP ERROR LOG FILE', E_USER_NOTICE);
        }

        /*
         * INIZIALIZE
         */
        define('DUPX_INIT_URL', SnapURL::getCurrentUrl(false, false, self::$dupInitFolderParentLevel));
        define('DUPX_ROOT_URL', SnapURL::getCurrentUrl(false, false, self::$dupInitFolderParentLevel + 1));

        // includes main files
        self::includes();
        // set time for logging time
        Log::resetTime();
        // set all PHP.INI settings
        self::phpIni();
        // init log files
        self::initLogs();
        // init global values
        \DUPX_Constants::init();

        // init addond before evrithing
        InstAddonsManager::getInstance()->inizializeAddons();
        // init templates
        self::templatesInit();
        // SECURITY CHECK
        Security::getInstance()->check();
        // init error handler after constant
        LogHandler::initErrorHandler();

        // init params
        PrmMng::getInstance()->initParams();

        // read params from request and init global value
        self::initInstallerFiles();

        // check custom hosts
        \DUPX_Custom_Host_Manager::getInstance()->init();

        $pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        Log::info("\n\n"
            . "==============================================\n"
            . "= BOOT INIT OK [" . $pathInfo . "]\n"
            . "==============================================\n", Log::LV_DETAILED);

        if (Log::isLevel(Log::LV_DEBUG)) {
            Log::info('-------------------');
            Log::info('PARAMS');
            Log::info(PrmMng::getInstance()->getParamsToText());
            Log::info('-------------------');
        }

        \DUPX_DB_Tables::getInstance();
    }

    /**
     * Init ini_set and default constants
     *
     * @return void
     */
    public static function phpIni()
    {
        // Absolute path to the Installer directory. - necessary for php protection
        if (!defined('KB_IN_BYTES')) {
            define('KB_IN_BYTES', 1024);
        }
        if (!defined('MB_IN_BYTES')) {
            define('MB_IN_BYTES', 1024 * KB_IN_BYTES);
        }
        if (!defined('GB_IN_BYTES')) {
            define('GB_IN_BYTES', 1024 * MB_IN_BYTES);
        }
        if (!defined('DUPLICATOR_PRO_PHP_MAX_MEMORY')) {
            define('DUPLICATOR_PRO_PHP_MAX_MEMORY', 4096 * MB_IN_BYTES);
        }

        date_default_timezone_set('UTC'); // Some machines don’t have this set so just do it here.
        if (strlen(@ini_get('date.timezone')) === 0) {
            // Some machines don’t date.timezone set
            @ini_set('date.timezone', 'UTC');
        }
        @ignore_user_abort(true);

        @set_time_limit(3600);

        $defaultCharset = ini_get("default_charset");
        if (empty($defaultCharset) && SnapUtil::isIniValChangeable('default_charset')) {
            @ini_set("default_charset", 'utf-8');
        }
        if (SnapUtil::isIniValChangeable('memory_limit')) {
            @ini_set('memory_limit', (string) DUPLICATOR_PRO_PHP_MAX_MEMORY);
        }
        if (SnapUtil::isIniValChangeable('max_input_time')) {
            @ini_set('max_input_time', '-1');
        }
        if (SnapUtil::isIniValChangeable('pcre.backtrack_limit')) {
            @ini_set('pcre.backtrack_limit', (string) PHP_INT_MAX);
        }

        //PHP INI SETUP: all time in seconds
        if (!isset($GLOBALS['DUPX_ENFORCE_PHP_INI']) || !$GLOBALS['DUPX_ENFORCE_PHP_INI']) {
            if (SnapUtil::isIniValChangeable('mysql.connect_timeout')) {
                @ini_set('mysql.connect_timeout', '5000');
            }
            if (SnapUtil::isIniValChangeable('max_execution_time')) {
                @ini_set("max_execution_time", '5000');
            }
            if (SnapUtil::isIniValChangeable('max_input_time')) {
                @ini_set("max_input_time", '5000');
            }
            if (SnapUtil::isIniValChangeable('default_socket_timeout')) {
                @ini_set('default_socket_timeout', '5000');
            }
            @set_time_limit(0);
        }
    }

    /**
     * Include default utils files and constants
     *
     * @return void
     */
    public static function includes()
    {
        require_once(DUPX_INIT . '/classes/config/class.conf.wp.php');
        require_once(DUPX_INIT . '/classes/utilities/class.u.exceptions.php');
        require_once(DUPX_INIT . '/classes/utilities/class.u.php');
        require_once(DUPX_INIT . '/classes/utilities/class.u.notices.manager.php');
        require_once(DUPX_INIT . '/classes/utilities/template/class.u.template.manager.php');
        require_once(DUPX_INIT . '/classes/validation/class.validation.manager.php');
        require_once(DUPX_INIT . '/classes/database/class.db.php');
        require_once(DUPX_INIT . '/classes/database/class.db.functions.php');
        require_once(DUPX_INIT . '/classes/database/class.db.tables.php');
        require_once(DUPX_INIT . '/classes/database/class.db.table.item.php');
        require_once(DUPX_INIT . '/classes/class.http.php');
        require_once(DUPX_INIT . '/classes/class.package.php');
        require_once(DUPX_INIT . '/classes/class.server.php');
        require_once(DUPX_INIT . '/classes/config/class.archive.config.php');
        require_once(DUPX_INIT . '/classes/config/class.constants.php');
        require_once(DUPX_INIT . '/classes/config/class.conf.utils.php');
        require_once(DUPX_INIT . '/ctrls/classes/class.ctrl.ajax.php');
        require_once(DUPX_INIT . '/ctrls/classes/class.ctrl.params.php');
        require_once(DUPX_INIT . '/ctrls/ctrl.base.php');
        require_once(DUPX_INIT . '/ctrls/classes/class.ctrl.extraction.php');
        require_once(DUPX_INIT . '/ctrls/classes/class.ctrl.dbinstall.php');
        require_once(DUPX_INIT . '/ctrls/classes/class.ctrl.s3.funcs.php');
        require_once(DUPX_INIT . '/classes/view-helpers/class.u.html.php');
        require_once(DUPX_INIT . '/classes/view-helpers/class.view.php');
        require_once(DUPX_INIT . '/classes/host/class.custom.host.manager.php');
        require_once(DUPX_INIT . '/classes/class.engine.php');
    }

    /**
     * This function moves the error_log.php into the dup-installer directory.
     * It is called before including any other file so it uses only native PHP functions.
     *
     * !!! Don't use any Duplicator function within this function. !!!
     *
     * @param bool $reset if true reset log file
     *
     * @return boolean
     */
    public static function initPhpErrorLog($reset = false)
    {
        if (!function_exists('ini_set')) {
            return false;
        }

        $logFile = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_INST_PHP_ERROR_LOG);

        if (file_exists($logFile)) {
            if (!is_writable($logFile)) {
                return false;
            } elseif ($reset && function_exists('unlink')) {
                @unlink($logFile);
            }
        }

        if (function_exists('error_reporting')) {
            error_reporting(E_ALL);
        }

        @ini_set("log_errors", '1');
        if (@ini_set("error_log", $logFile) === false) {
            return false;
        }

        if (!file_exists($logFile)) {
            SnapUtil::errorLog("PHP ERROR LOG INIT");
        }

        return true;
    }

    /**
     * It is called before including any other file so it uses only native PHP functions.
     *
     * !!! Don't use any Duplicator function within this function. !!!
     *
     * @return bool|string package hash or false if fail
     */
    public static function getPackageHash()
    {
        static $packageHash = null;
        if (is_null($packageHash)) {
            $searchStr    = DUPX_INIT . '/' . self::DESCRIPTORS_PREFIX . '*';
            $config_files = glob($searchStr, GLOB_ONLYDIR);
            if (empty($config_files)) {
                $packageHash = false;
            } else {
                $descriptor_folder_path = array_pop($config_files);
                $descriptor_folder_name = basename($descriptor_folder_path);
                $packageHash            = substr($descriptor_folder_name, strlen(self::DESCRIPTORS_PREFIX));
            }
        }
        return $packageHash;
    }

    /**
     * This function init all params before read from request
     *
     * @return void
     */
    protected static function initParamsBase()
    {
        // GET PARAMS FROM REQUEST
        \DUPX_Ctrl_Params::setParamsBase();

        // set log level from params
        Log::setLogLevel();
        Log::setPostProcessCallback(array('DUPX_CTRL', 'renderPostProcessings'));
        Log::setAfterFatalErrorCallback(function () {
            if (InstState::getInstance()->getMode() === InstState::MODE_OVR_INSTALL) {
                DUPX_U::maintenanceMode(false);
            }
        });

        $paramsManager         = PrmMng::getInstance();
        $GLOBALS['DUPX_DEBUG'] = $paramsManager->getValue(PrmMng::PARAM_DEBUG);
    }

    /**
     * Makes sure no caching mechanism is used during install
     *
     * @return void
     */
    protected static function setHTTPHeaders()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
     * Init log header
     *
     * @return void
     */
    protected static function initLogs()
    {
        if (!chdir(DUPX_INIT)) {
            // RSR TODO: Can't change directories
            throw new \Exception("Can't change to directory " . DUPX_INIT);
        }

        //Restart log if user starts from step 0
        if (self::isInit()) {
            self::initPhpErrorLog(true);
            Log::clearLog();
            Log::info("********************************************************************************");
            Log::info('* DUPLICATOR-PRO: Install-Log');
            Log::info('* STEP-0 START @ ' . @date('h:i:s'));
            Log::info('* NOTICE: Do NOT post to public sites or forums!!');
            Log::info("********************************************************************************");
        }
    }

    /**
     * Init all installer files
     *
     * @return void
     */
    protected static function initInstallerFiles()
    {
        if (!chdir(DUPX_INIT)) {
            // RSR TODO: Can't change directories
            throw new \Exception("Can't change to directory " . DUPX_INIT);
        }

        //Restart log if user starts from step 0
        if (self::isInit()) {
            self::logHeader();
            \DUPX_NOTICE_MANAGER::getInstance()->resetNotices();

            // LOAD PARAMS AFTER LOG RESET
            $paramManager = PrmMng::getInstance();
            $paramManager->load(true);
            try {
                InstallerOrigFileMng::getInstance()->restoreAll(array(
                    ServerConfigs::CONFIG_ORIG_FILE_USERINI_ID,
                    ServerConfigs::CONFIG_ORIG_FILE_PHPINI_ID,
                    ServerConfigs::CONFIG_ORIG_FILE_WEBCONFIG_ID,
                    ServerConfigs::CONFIG_ORIG_FILE_HTACCESS_ID,
                    ServerConfigs::CONFIG_ORIG_FILE_WPCONFIG_ID,
                ));
            } catch (\Exception $e) {
                Log::logException($e, Log::LV_DEFAULT, 'CANT RESTORE CONFIG FILES FORM PREVISION INSTALLATION');
                \DUPX_NOTICE_MANAGER::getInstance()->addNextStepNotice(array(
                    'shortMsg'    => 'The installer cannot restore files from a previous installation. ',
                    'longMsg'     => 'This problem does not affect the current installation so you can continue.<br>'
                    . 'This can happen if the root folder does not have write permissions.',
                    'longMsgMode' => \DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'level'       => \DUPX_NOTICE_ITEM::NOTICE,
                ));
            }

            self::initParamsBase();

            \DUP_PRO_Extraction::resetData();
            \DUPX_DBInstall::resetData();
            \DUPX_S3_Funcs::resetData();
            self::renameHtaccess();

            // update state only if isn't set by param overwrite
            InstState::getInstance()->checkState(true, false);
            // On init remove maintenance mode
            \DUPX_U::maintenanceMode(false);
        } else {
            // INIT PARAMS
            $paramManager = PrmMng::getInstance();
            $paramManager->load();
            self::initParamsBase();
        }

        $paramManager->save();
    }

    /**
     * Rename .htaccess file in dup-installer folder if it exists, so that it does not interfere with installer
     *
     * @return void
     */
    protected static function renameHtaccess()
    {
        $htaccessPath = DUPXABSPATH . "/.htaccess";
        if (!file_exists($htaccessPath)) {
            return;
        }

        $htaccessPathRenamed = DUPXABSPATH . "/renamed_" . date_format(new \DateTime(), 'mdYHis') . ".htaccess";
        if (!SnapIO::rename($htaccessPath, $htaccessPathRenamed)) {
            $errorMsg  = "WARNING: Could not delete/rename file \"$htaccessPath\". That file could interfere with the installation.\n";
            $errorMsg .= "If you encounter problems like buttons not working, please remove it yourself manually and restart the installer.";
            Log::info($errorMsg);
        } else {
            Log::info(".htaccess file was found in dup-installer folder and it was renamed to avoid interference with installer.");
        }
    }

    /**
     * Write log header
     *
     * @return void
     */
    protected static function logHeader()
    {
        $archiveConfig = \DUPX_ArchiveConfig::getInstance();
        $colSize       = 60;
        $labelPadSize  = 20;
        $os            = defined('PHP_OS') ? PHP_OS : 'unknown';

        $log  = '';
        $log .= str_pad(
            str_pad('PACKAGE INFO', $labelPadSize, '_', STR_PAD_RIGHT) . ' ' . 'ORIGINAL SERVER',
            $colSize,
            ' ',
            STR_PAD_RIGHT
        ) . '|' . 'CURRENT SERVER' . "\n";
        $log .= str_pad(
            str_pad('OS', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . $archiveConfig->version_os,
            $colSize,
            ' ',
            STR_PAD_RIGHT
        ) . '|' . $os . "\n";
        $log .= str_pad(
            str_pad('PHP VERSION', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . $archiveConfig->version_php,
            $colSize,
            ' ',
            STR_PAD_RIGHT
        ) . '|' . phpversion() . "\n";
        $log .= "********************************************************************************";
        Log::info($log, Log::LV_DEFAULT);

        Log::info("CURRENT SERVER INFO");
        Log::info(str_pad('PHP', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . phpversion() . ' | SAPI: ' . php_sapi_name());
        Log::info(str_pad('PHP MEMORY', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . $GLOBALS['PHP_MEMORY_LIMIT'] . ' | SUHOSIN: ' . $GLOBALS['PHP_SUHOSIN_ON']);
        Log::info(str_pad('ARCHITECTURE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . SnapUtil::getArchitectureString());
        Log::info(str_pad('SERVER', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . $_SERVER['SERVER_SOFTWARE']);
        Log::info(str_pad('DOC ROOT', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str(DUPX_ROOT));
        Log::info(str_pad('REQUEST URL', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str(DUPX_ROOT_URL));
        Log::info("********************************************************************************");
    }

    /**
     * return true if is the first installer call from installer.php
     *
     * @return bool
     */
    public static function isInit()
    {
        // don't use param manager because isn't initialized
        $isFirstStep   = isset($_REQUEST[PrmMng::PARAM_CTRL_ACTION]) && $_REQUEST[PrmMng::PARAM_CTRL_ACTION] === "ctrl-step1";
        $isInitialized = isset($_REQUEST[PrmMng::PARAM_STEP_ACTION]) && !empty($_REQUEST[PrmMng::PARAM_STEP_ACTION]);
        return $isFirstStep && !$isInitialized;
    }

    /**
     * This function disables the shutdown function defined in the boot class
     *
     * @return void
     */
    public static function disableBootShutdownFunction()
    {
        self::$shutdownFunctionEnaled = false;
    }

    /**
     * This function sets the shutdown function before the installer is initialized.
     * Prevents blank pages.
     *
     * After the plugin is initialized it will be set as a shudwon ​​function LogHandler::shutdown
     *
     * !!! Don't use any Duplicator function within this function. !!!
     *
     * @return void
     */
    public static function bootShutdown()
    {
        if (!self::$shutdownFunctionEnaled) {
            return;
        }

        if (($error = error_get_last())) {
            ?>
            <h1>BOOT SHUTDOWN FATAL ERROR</H1>
            <pre><?php
            echo 'Error: ' . htmlspecialchars($error['message']) . "\n\n\n" .
            'Type: ' . $error['type'] . "\n" .
            'File: ' . htmlspecialchars($error['file']) . "\n" .
            'Line: ' . $error['line'] . "\n";
            ?></pre>
                <?php
        }
    }

    /**
     * this function is called before anything else. do not use duplicator functions because nothing is included at this level.
     *
     * @return boolean
     */
    public static function phpVersionCheck()
    {
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=')) {
            return true;
        }
        $match = null;
        if (preg_match("#^\d+(\.\d+)*#", PHP_VERSION, $match)) {
            $phpVersion = $match[0];
        } else {
            $phpVersion = PHP_VERSION;
        }
        // no html
        echo 'This server is running PHP: ' . $phpVersion . '. A minimum of PHP ' . self::MINIMUM_PHP_VERSION . ' is required to run the installer.'
        . ' Contact your hosting provider or server administrator and let them know you would like to upgrade your PHP version.';
        die();
    }

    /**
     * Init templates
     *
     * @return void
     */
    protected static function templatesInit()
    {
        $tpl = \DUPX_Template::getInstance();

        $tpl->addTemplate(\DUPX_Template::TEMPLATE_BASE, DUPX_INIT . '/templates/base', \DUPX_Template::TEMPLATE_ADVANCED);
        $tpl->addTemplate(\DUPX_Template::TEMPLATE_IMPORT_ADVANCED, DUPX_INIT . '/templates/import-advanced', \DUPX_Template::TEMPLATE_ADVANCED);
        $tpl->addTemplate(\DUPX_Template::TEMPLATE_IMPORT_BASE, DUPX_INIT . '/templates/import-base', \DUPX_Template::TEMPLATE_IMPORT_ADVANCED);
        $tpl->addTemplate(\DUPX_Template::TEMPLATE_RECOVERY, DUPX_INIT . '/templates/recovery', \DUPX_Template::TEMPLATE_BASE);

        $tpl->setTemplate(\DUPX_Template::TEMPLATE_ADVANCED);
    }
}
