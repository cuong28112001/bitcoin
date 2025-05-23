<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Addons\FtpAddon\Models;

use DUP_PRO_Global_Entity;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\AbstractStorageAdapter;
use Exception;

class FTPStorage extends AbstractStorageEntity
{
    const MIN_DOWNLOAD_CHUNK_SIZE_IN_MB     = 2;
    const DEFAULT_DOWNLOAD_CHUNK_SIZE_IN_MB = 10;
    const MAX_DOWNLOAD_CHUNK_SIZE_IN_MB     = 9999;
    const MIN_UPLOAD_CHUNK_SIZE_IN_MB       = 1;
    const DEFAULT_UPLOAD_CHUNK_SIZE_IN_MB   = 5;
    const MAX_UPLOAD_CHUNK_SIZE_IN_MB       = 100;

    /**
     * Get default config
     *
     * @return array<string,scalar>
     */
    protected static function getDefaultConfig()
    {
        $config = parent::getDefaultConfig();
        $config = array_merge(
            $config,
            [
                'server'          => '',
                'port'            => 21,
                'username'        => '',
                'password'        => '',
                'use_curl'        => false,
                'timeout_in_secs' => 15,
                'ssl'             => false,
                'passive_mode'    => true,
            ]
        );
        return $config;
    }


    /**
     * Get stoage adapter
     *
     * @return AbstractStorageAdapter
     */
    protected function getAdapter()
    {
        if ($this->adapter !== null) {
            return $this->adapter;
        }

        if ($this->config['use_curl'] || static::isCurlOnly()) {
            $global        = DUP_PRO_Global_Entity::getInstance();
            $this->adapter = new FTPCurlStorageAdapter(
                $this->config['server'],
                $this->config['port'],
                $this->config['username'],
                $this->config['password'],
                $this->config['storage_folder'],
                $this->config['timeout_in_secs'],
                $this->config['ssl'],
                $this->config['passive_mode'],
                0,
                !$global->ssl_disableverify,
                $global->ssl_useservercerts ? '' : DUPLICATOR_PRO_CERT_PATH
            );
        } else {
            $this->adapter = new FTPStorageAdapter(
                $this->config['server'],
                $this->config['port'],
                $this->config['username'],
                $this->config['password'],
                $this->config['storage_folder'],
                $this->config['timeout_in_secs'],
                $this->config['ssl'],
                $this->config['passive_mode']
            );
        }

        return $this->adapter;
    }

    /**
     * Will be called, automatically, when Serialize
     *
     * @return array<string, mixed>
     */
    public function __serialize() // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__serializeFound
    {
        $data = parent::__serialize();
        unset($data['client']);
        return $data;
    }

    /**
     * Serialize
     *
     * Wakeup method.
     *
     * @return void
     */
    public function __wakeup()
    {
        parent::__wakeup();

        if ($this->legacyEntity) {
            // Old storage entity
            $this->legacyEntity = false;
            // Make sure the storage type is right from the old entity
            $this->storage_type = $this->getSType();
            $this->config       = [
                'server'          => $this->ftp_server,
                'port'            => $this->ftp_port,
                'username'        => $this->ftp_username,
                'password'        => $this->ftp_password,
                'use_curl'        => $this->ftp_use_curl,
                'storage_folder'  => '/' . ltrim($this->ftp_storage_folder, '/\\'),
                'max_packages'    => $this->ftp_max_files,
                'timeout_in_secs' => $this->ftp_timeout_in_secs,
                'ssl'             => $this->ftp_ssl,
                'passive_mode'    => $this->ftp_passive_mode,
            ];
            // reset old values
            $this->ftp_server          = '';
            $this->ftp_port            = 21;
            $this->ftp_username        = '';
            $this->ftp_password        = '';
            $this->ftp_use_curl        = false;
            $this->ftp_storage_folder  = '';
            $this->ftp_max_files       = 10;
            $this->ftp_timeout_in_secs = 15;
            $this->ftp_ssl             = false;
            $this->ftp_passive_mode    = false;
        }

        // For legacy entities, we need to make sure the config is up to date
        $this->config['port']            = (int) $this->config['port'];
        $this->config['max_packages']    = (int) $this->config['max_packages'];
        $this->config['timeout_in_secs'] = (int) $this->config['timeout_in_secs'];
    }

    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType()
    {
        return 2;
    }

    /**
     * Returns the FontAwesome storage type icon.
     *
     * @return string Returns the font-awesome icon
     */
    public static function getStypeIcon()
    {
        return '<i class="fas fa-network-wired fa-fw"></i>';
    }

    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName()
    {
        return __('FTP', 'duplicator-pro');
    }

    /**
     * Get priority, used to sort storages.
     * 100 is neutral value, 0 is the highest priority
     *
     * @return int
     */
    public static function getPriority()
    {
        return 80;
    }

    /**
     * Get storage location string
     *
     * @return string
     */
    public function getLocationString()
    {
        return "ftp://" . $this->config['server'] . ":" . $this->config['port'] . $this->getStorageFolder();
    }

    /**
     * Check if storage is supported
     *
     * @return bool
     */
    public static function isSupported()
    {
        return apply_filters('duplicator_pro_ftp_connect_exists', function_exists('ftp_connect') || SnapUtil::isCurlEnabled(false));
    }

    /**
     * Check if only curl is supported.
     *
     * @return bool
     */
    public static function isCurlOnly()
    {
        return !function_exists('ftp_connect') && SnapUtil::isCurlEnabled(false);
    }

    /**
     * Get supported notice, displayed if storage isn't supported
     *
     * @return string html string or empty if storage is supported
     */
    public static function getNotSupportedNotice()
    {
        if (static::isSupported()) {
            return '';
        }

        return sprintf(
            esc_html__(
                'FTP Storage requires FTP module enabled. Please install the FTP module as described in the %s.',
                'duplicator-pro'
            ),
            '<a href="https://secure.php.net/manual/en/ftp.installation.php" target="_blank">https://secure.php.net/manual/en/ftp.installation.php</a>'
        );
    }

    /**
     * Check if storage is valid
     *
     * @return bool Return true if storage is valid and ready to use, false otherwise
     */
    public function isValid()
    {
        return $this->getAdapter()->isValid();
    }

    /**
     * Get upload chunk size in bytes
     *
     * @return int bytes
     */
    public function getUploadChunkSize()
    {
        $dGlobal = DynamicGlobalEntity::getInstance();
        return $dGlobal->getVal('ftp_upload_chunksize_in_mb', self::DEFAULT_UPLOAD_CHUNK_SIZE_IN_MB) * 1024 * 1024;
    }

    /**
     * Get upload chunk size in bytes
     *
     * @return int bytes
     */
    public function getDownloadChunkSize()
    {
        $dGlobal = DynamicGlobalEntity::getInstance();
        return $dGlobal->getVal('ftp_download_chunksize_in_mb', self::DEFAULT_DOWNLOAD_CHUNK_SIZE_IN_MB) * 1024 * 1024;
    }


    /**
     * Get upload chunk timeout in seconds
     *
     * @return int timeout in microseconds, 0 unlimited
     */
    public function getUploadChunkTimeout()
    {
        return (int) ($this->config['timeout_in_secs'] <= 0 ? 0 :  $this->config['timeout_in_secs'] * SECONDS_IN_MICROSECONDS);
    }

    /**
     * Get action key text
     *
     * @param string $key Key name (action, pending, failed, cancelled, success)
     *
     * @return string
     */
    protected function getUploadActionKeyText($key)
    {
        switch ($key) {
            case 'action':
                return sprintf(
                    __('Transferring to FTP server %1$s in folder:<br/> <i>%2$s</i>', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            case 'pending':
                return sprintf(
                    __('Transfer to FTP server %1$s in folder %2$s is pending', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            case 'failed':
                return sprintf(
                    __('Failed to transfer to FTP server %1$s in folder %2$s', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            case 'cancelled':
                return sprintf(
                    __('Cancelled before could transfer to FTP server:<br/>%1$s in folder %2$s', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            case 'success':
                return sprintf(
                    __('Transferred Backup to FTP server:<br/>%1$s in folder %2$s', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            default:
                throw new Exception('Invalid key');
        }
    }

    /**
     * Get action key text for download
     *
     * @param string $key Key name (action, pending, failed, cancelled, success)
     *
     * @return string
     */
    protected function getDownloadActionKeyText($key)
    {
        switch ($key) {
            case 'action':
                return sprintf(
                    __('Downloading from FTP server %1$s from folder:<br/> <i>%2$s</i>', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            case 'pending':
                return sprintf(
                    __('Download from FTP server %1$s from folder %2$s is pending', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            case 'failed':
                return sprintf(
                    __('Failed to download from FTP server %1$s from folder %2$s', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            case 'cancelled':
                return sprintf(
                    __('Cancelled before could download from FTP server:<br/>%1$s from folder %2$s', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            case 'success':
                return sprintf(
                    __('Downloaded from FTP server:<br/>%1$s from folder %2$s', "duplicator-pro"),
                    $this->config['server'],
                    $this->getStorageFolder()
                );
            default:
                throw new Exception('Invalid key');
        }
    }

    /**
     * List quick view
     *
     * @param bool $echo Echo or return
     *
     * @return string
     */
    public function getListQuickView($echo = true)
    {
        ob_start();
        ?>
        <div>
            <label><?php esc_html_e('Server', 'duplicator-pro'); ?>:</label>
            <?php echo esc_html($this->config['server']); ?>: <?php echo intval($this->config['port']);  ?>  <br/>
            <label><?php esc_html_e('Location', 'duplicator-pro') ?>:</label>
            <?php
                echo wp_kses(
                    $this->getHtmlLocationLink(),
                    [
                        'a' => [
                            'href'   => [],
                            'target' => [],
                        ],
                    ]
                );
            ?>
        </div>
        <?php
        if ($echo) {
            ob_end_flush();
            return '';
        } else {
            return ob_get_clean();
        }
    }

    /**
     * Returns the config fields template data
     *
     * @return array<string, mixed>
     */
    protected function getConfigFieldsData()
    {
        $forceCurl = static::isCurlOnly();
        return array_merge($this->getDefaultConfigFieldsData(), [
            'useCurl'   => $this->config['use_curl'] || $forceCurl,
            'forceCurl' => $forceCurl,
        ]);
    }

    /**
     * Returns the default config fields template data
     *
     * @return array<string, mixed>
     */
    protected function getDefaultConfigFieldsData()
    {
        return [
            'storage'       => $this,
            'server'        => $this->config['server'],
            'port'          => $this->config['port'],
            'username'      => $this->config['username'],
            'password'      => $this->config['password'],
            'storageFolder' => $this->config['storage_folder'],
            'maxPackages'   => $this->config['max_packages'],
            'timeout'       => $this->config['timeout_in_secs'],
            'useCurl'       => $this->config['use_curl'],
            'isPassive'     => $this->config['passive_mode'],
            'useSSL'        => $this->config['ssl'],
            'forceCurl'     => false,
        ];
    }

    /**
     * Returns the config fields template path
     *
     * @return string
     */
    protected function getConfigFieldsTemplatePath()
    {
        return 'ftpaddon/configs/ftp';
    }

    /**
     * Update data from http request, this method don't save data, just update object properties
     *
     * @param string $message Message
     *
     * @return bool True if success and all data is valid, false otherwise
     */
    public function updateFromHttpRequest(&$message = '')
    {
        if ((parent::updateFromHttpRequest($message) === false)) {
            return false;
        }

        $this->config['max_packages'] = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'ftp_max_files', 10);
        $this->config['server']       = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'ftp_server', '');
        $this->config['port']         = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'ftp_port', 10);
        $this->config['username']     = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'ftp_username', '');
        $password                     = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'ftp_password', '');
        $password2                    = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'ftp_password2', '');
        if (strlen($password) > 0) {
            if ($password !== $password2) {
                $message = __('Passwords do not match', 'duplicator-pro');
                return false;
            }
            $this->config['password'] = $password;
        }
        $this->config['storage_folder']  = self::getSanitizedInputFolder('_ftp_storage_folder', 'add');
        $this->config['timeout_in_secs'] = max(10, SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'ftp_timeout_in_secs', 15));
        $this->config['use_curl']        = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, '_ftp_use_curl', false) || static::isCurlOnly();
        $this->config['ssl']             = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, '_ftp_ssl', false);
        $this->config['passive_mode']    = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, '_ftp_passive_mode', false);

        $errorMsg = '';
        if ($this->getAdapter()->initialize($errorMsg) === false) {
            $message = sprintf(
                __('Failed to connect to FTP server with message: %1$s', 'duplicator-pro'),
                $errorMsg
            );
            return false;
        }

        $message = sprintf(
            __('FTP Storage Updated - Server %1$s, Folder %2$s was created.', 'duplicator-pro'),
            $this->config['server'],
            $this->getStorageFolder()
        );
        return true;
    }

    /**
     * Register storage type
     *
     * @return void
     */
    public static function registerType()
    {
        parent::registerType();

        add_action('duplicator_update_global_storage_settings', function () {
            $dGlobal = DynamicGlobalEntity::getInstance();

            foreach (static::getDefaultSettings() as $key => $default) {
                $value = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, $key, $default);
                $dGlobal->setVal($key, $value);
            }
            $dGlobal->save();
        });
    }

    /**
     * Get default settings
     *
     * @return array<string, scalar>
     */
    protected static function getDefaultSettings()
    {
        return [
            'ftp_upload_chunksize_in_mb'   => self::DEFAULT_UPLOAD_CHUNK_SIZE_IN_MB,
            'ftp_download_chunksize_in_mb' => self::DEFAULT_DOWNLOAD_CHUNK_SIZE_IN_MB,
        ];
    }

    /**
     * Render the settings page for this storage.
     *
     * @return void
     */
    public static function renderGlobalOptions()
    {
        $dGlobal = DynamicGlobalEntity::getInstance();
        TplMng::getInstance()->render(
            'ftpaddon/configs/ftp_global_options',
            [
                'uploadChunkSize'   => $dGlobal->getVal('ftp_upload_chunksize_in_mb', self::DEFAULT_UPLOAD_CHUNK_SIZE_IN_MB),
                'downloadChunkSize' => $dGlobal->getVal('ftp_download_chunksize_in_mb', self::DEFAULT_DOWNLOAD_CHUNK_SIZE_IN_MB),
            ]
        );
    }
}
