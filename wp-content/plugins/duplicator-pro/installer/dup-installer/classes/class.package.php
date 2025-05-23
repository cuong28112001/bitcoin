<?php

/**
 * Class used to update and edit web server configuration files
 * for .htaccess, web.config and user.ini
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\Crypt
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Core\Bootstrap;
use Duplicator\Installer\Core\Deploy\ServerConfigs;
use Duplicator\Installer\Utils\InstallerOrigFileMng;
use Duplicator\Installer\Utils\InstDescMng;

/**
 * Package related functions
 */
final class DUPX_Package
{
    /**
     *
     * @return bool|string false if fail
     */
    public static function getPackageHash()
    {
        static $packageHash = null;
        if (is_null($packageHash)) {
            if (($packageHash = Bootstrap::getPackageHash()) === false) {
                throw new Exception('PACKAGE ERROR: can\'t find package hash');
            }
        }
        return $packageHash;
    }

    /**
     *
     * @return string
     */
    public static function getArchiveFileHash()
    {
        static $fileHash = null;

        if (is_null($fileHash)) {
            $fileHash = preg_replace('/^.+_([a-z0-9]+)_[0-9]{14}_archive\.(?:daf|zip)$/', '$1', Security::getInstance()->getArchivePath());
        }

        return $fileHash;
    }

    /**
     *
     * @return bool|string false if fail
     */
    public static function getPackageArchivePath()
    {
        static $archivePath = null;
        if (is_null($archivePath)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_ARCHIVE_CONFIG);
            if (!file_exists($path)) {
                throw new Exception('PACKAGE ERROR: can\'t read package path: ' . $path);
            } else {
                $archivePath = $path;
            }
        }
        return $archivePath;
    }

    /**
     * Returns a save-to-edit wp-config file
     *
     * @return string
     */
    public static function getWpconfigArkPath()
    {
        return InstallerOrigFileMng::getInstance()->getEntryStoredPath(ServerConfigs::CONFIG_ORIG_FILE_WPCONFIG_ID);
    }

    /**
     *
     * @return string
     */
    public static function getManualExtractFile()
    {
        return DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_MANUAL_EXTRACT);
    }

    /**
     *
     * @return string
     */
    public static function getWpconfigSamplePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/assets/wp-config-sample.php';
        }
        return $path;
    }

    /**
     * Get path to directory with SQL dump files
     *
     * @return string
     */
    public static function getSqlDumpDirPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . dirname(InstDescMng::getInstance()->getName(InstDescMng::TYPE_DB_DUMP));
        }
        return $path;
    }

    /**
     *
     * @return string
     */
    public static function getDirsListPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_DIR_LIST);
        }
        return $path;
    }

    /**
     *
     * @return string
     */
    public static function getFilesListPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_FILE_LIST);
        }
        return $path;
    }

    /**
     *
     * @return string
     */
    public static function getScanJsonPath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_SCAN);
        }
        return $path;
    }

    /**
     *
     * @param callable $callback callback function
     *
     * @return boolean
     */
    public static function foreachDirCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('Not valid callback');
        }

        $dirFiles = DUPX_Package::getDirsListPath();

        if (($handle = fopen($dirFiles, "r")) === false) {
            throw new Exception('Can\'t open dirs file list');
        }

        while (($line = fgets($handle)) !== false) {
            if (($info = json_decode($line)) === null) {
                throw new Exception('Invalid json line in dirs file: ' . $line);
            }

            call_user_func($callback, $info);
        }

        fclose($handle);
        return true;
    }

    /**
     *
     * @param callable $callback callback
     *
     * @return boolean
     */
    public static function foreachFileCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('Not valid callback');
        }

        $filesPath = DUPX_Package::getFilesListPath();

        if (($handle = fopen($filesPath, "r")) === false) {
            throw new Exception('Can\'t open files file list');
        }

        while (($line = fgets($handle)) !== false) {
            if (($info = json_decode($line)) === null) {
                throw new Exception('Invalid json line in files file: ' . $line);
            }

            call_user_func($callback, $info);
        }

        fclose($handle);
        return true;
    }
}
