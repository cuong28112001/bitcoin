<?php

/**
 * controller step 2 db install test
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package CTRL
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\Descriptors\ParamDescUsers;
use Duplicator\Installer\Core\Deploy\Database\DbCleanup;
use Duplicator\Installer\Core\Deploy\Database\DbDumpIterator;
use Duplicator\Installer\Core\Deploy\Database\DbUserMode;
use Duplicator\Installer\Core\Deploy\Database\DbUtils;
use Duplicator\Installer\Core\Deploy\Database\QueryFixes;
use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\InstDescMng;
use VendorDuplicator\Amk\JsonSerialize\AbstractJsonSerializable;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Snap\SnapUtil;

require_once(DUPX_INIT . '/api/class.cpnl.ctrl.php');

class DUPX_DBInstall extends AbstractJsonSerializable
{
    const ENGINE_NORMAL                     = 'normal';
    const ENGINE_CHUNK                      = 'chunk';
    const DBACTION_CREATE                   = 'create';
    const DBACTION_EMPTY                    = 'empty';
    const DBACTION_REMOVE_ONLY_TABLES       = 'removetables';
    const DBACTION_RENAME                   = 'rename';
    const DBACTION_MANUAL                   = 'manual';
    const DBACTION_ONLY_CONNECT             = 'onlyconnect';
    const DBACTION_DO_NOTHING               = 'dbdonothing';
    const TEMP_DB_PREFIX                    = 'dpro___tmp__';
    const TABLE_CREATION_END_MARKER         = "/***** TABLE CREATION END *****/\n";
    const QUERY_ERROR_LOG_LEN               = 200;
    const SQL_CREATE_VIEW_PROC_FUNC_PATTERN = "/^\s*(?:\/\*!\d+\s)?\s*CREATE\s.*?(?:VIEW|PROCEDURE|FUNCTION).*$/ms";
    const BUILD_MODE_MYSQLDUMP              = 'MYSQLDUMP';
    const TABLES_REGEX_CHUNK_SIZE           = 100;

    /** @var ?mysqli */
    private $dbh = null;
    /** @var mixed[] */
    public $post = array();
    /** @var string */
    public $dbaction = self::DBACTION_EMPTY;
    /** @var string */
    public $dbcharset = '';
    /** @var string */
    public $dbcollate = '';
    /** @var int */
    public $dbvar_maxtime = 300;
    /** @var int */
    public $dbvar_maxpacks = MB_IN_BYTES;
    /** @var string */
    public $dbvar_sqlmode = 'NOT_SET';
    /** @var DbDumpIterator */
    public $dbDumpIterator;
    /** @var int */
    public $table_count = 0;
    /** @var int */
    public $table_rows = 0;
    /** @var int */
    public $query_errs = 0;
    /** @var int */
    public $drop_tbl_log = 0;
    /** @var int */
    public $rename_tbl_log = 0;
    /** @var int */
    public $dbquery_errs = 0;
    /** @var int */
    public $dbquery_rows = 0;
    /** @var int */
    public $dbtable_count = 0;
    /** @var int */
    public $dbtable_rows = 0;
    /** @var float */
    public $profile_start = 0;
    /** @var float */
    public $start_microtime = 0;
    /** @var float */
    public $thread_start_time = 0;
    /** @var bool */
    public $dbsplit_creates = true;
    /** @var string[] */
    public $setQueries = array();
    /** @var DbUserMode */
    protected $dbUserMode = null;
    /** @var QueryFixes */
    protected $queryFixes = null;

    /** @var ?self */
    protected static $instance = null;

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Class constructor
     */
    private function __construct()
    {
        if (!DUPX_Validation_manager::isValidated()) {
            throw new Exception('Installer isn\'t validated');
        }
        $this->initData();
    }

    /**
     * Inizialize extraction data
     *
     * @return void
     */
    protected function initData()
    {
        // if data file exists load saved data
        if (file_exists(self::dbinstallDataFilePath())) {
            Log::info('LOAD DBINSTALL DATA FROM JSON', Log::LV_DETAILED);
            if ($this->loadData() == false) {
                throw new Exception('Can\'t load dbinstall data');
            }
        } else {
            Log::info('INIT DB INSTALL DATA', Log::LV_DETAILED);
            $this->constructData();
            $this->initLogDbInstall();
            $this->saveData();
        }
    }


    /**
     * DATA INIT
     *
     * @return void
     */
    protected function constructData()
    {
        $paramsManager         = PrmMng::getInstance();
        $this->start_microtime = DUPX_U::getMicrotime();
        $this->profile_start   = DUPX_U::getMicrotime();

        $this->post = array(
            'view_mode'         => $paramsManager->getValue(PrmMng::PARAM_DB_VIEW_MODE),
            'dbname'            => $paramsManager->getValue(PrmMng::PARAM_DB_NAME),
            'dbuser'            => $paramsManager->getValue(PrmMng::PARAM_DB_USER),
            'dbpass'            => $paramsManager->getValue(PrmMng::PARAM_DB_PASS),
            'dbport'            => parse_url($paramsManager->getValue(PrmMng::PARAM_DB_HOST), PHP_URL_PORT),
            'dbmysqlmode'       => $paramsManager->getValue(PrmMng::PARAM_DB_MYSQL_MODE),
            'dbmysqlmode_opts'  => $paramsManager->getValue(PrmMng::PARAM_DB_MYSQL_MODE_OPTS),
            'cpnl-host'         => $paramsManager->getValue(PrmMng::PARAM_CPNL_HOST),
            'cpnl-user'         => $paramsManager->getValue(PrmMng::PARAM_CPNL_USER),
            'cpnl-pass'         => $paramsManager->getValue(PrmMng::PARAM_CPNL_PASS),
            'cpnl-dbuser-chk'   => $paramsManager->getValue(PrmMng::PARAM_CPNL_DB_USER_CHK),
            'pos'               => 0,
            'pass'              => false,
            'first_chunk'       => true,
            'dbchunk_retry'     => 0,
            'continue_chunking' => $paramsManager->getValue(PrmMng::PARAM_DB_CHUNK),
            'progress'          => 0,
            'delimiter'         => ';',
            'is_error'          => 0,
            'error_msg'         => '',
        );

        $this->dbaction        = $paramsManager->getValue(PrmMng::PARAM_DB_ACTION);
        $this->dbcharset       = $paramsManager->getValue(PrmMng::PARAM_DB_CHARSET);
        $this->dbcollate       = $paramsManager->getValue(PrmMng::PARAM_DB_COLLATE);
        $this->dbsplit_creates = $paramsManager->getValue(PrmMng::PARAM_DB_SPLIT_CREATES);

        $this->dbUserMode     = new DbUserMode();
        $this->dbDumpIterator = new DbDumpIterator();
    }

    /**
     * Write Log file header
     *
     * @return void
     */
    protected function initLogDbInstall()
    {
        $paramsManager = PrmMng::getInstance();
        $labelPadSize  = 20;
        Log::info("\n\n\n********************************************************************************");
        Log::info('* DUPLICATOR PRO INSTALL-LOG');
        Log::info('* STEP-2 START @ ' . @date('h:i:s'));
        Log::info('* NOTICE: Do NOT post to public sites or forums!!');
        Log::info("********************************************************************************");
        Log::info("USER INPUTS");
        Log::info(str_pad('DB ENGINE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_ENGINE)));
        Log::info(str_pad('VIEW MODE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_VIEW_MODE)));
        Log::info(str_pad('DB ACTION', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_ACTION)));
        Log::info(str_pad('DB HOST', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str('**OBSCURED**'));
        Log::info(str_pad('DB NAME', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str('**OBSCURED**'));
        Log::info(str_pad('DB PASS', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str('**OBSCURED**'));
        Log::info(str_pad('DB PORT', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str('**OBSCURED**'));
        Log::info(str_pad('USER MODE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_USERS_MODE)));
        Log::info(str_pad('TABLE PREFIX', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX)));
        Log::info(str_pad('MYSQL MODE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_MYSQL_MODE)));
        Log::info(str_pad('MYSQL MODE OPTS', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_MYSQL_MODE_OPTS)));
        Log::info(str_pad('CHARSET', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_CHARSET)));
        Log::info(str_pad('COLLATE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_COLLATE)));
        Log::info(str_pad('CUNKING', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_CHUNK)));
        Log::info(str_pad('VIEW CREATION', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_VIEW_CREATION)));
        Log::info(str_pad('STORED PROCEDURE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_PROC_CREATION)));
        Log::info(str_pad('FUNCTIONS', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_FUNC_CREATION)));
        Log::info(str_pad('REMOVE DEFINER', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_REMOVE_DEFINER)));
        Log::info(str_pad('SPLIT CREATES', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_SPLIT_CREATES)));

        $dbDumpTotalSize  = DUPX_U::readableByteSize($this->dbDumpIterator->totalSize());
        $dbDumpTotalCount = $this->dbDumpIterator->count();
        Log::info(str_pad('SQL FILES', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . $dbDumpTotalCount . ' (' . $dbDumpTotalSize . ')');
        foreach ($this->dbDumpIterator as $i => $path) {
            $currentSize = DUPX_U::readableByteSize(@filesize($path));
            Log::info("\t" . ($i + 1) . ")" . basename($path) . " (" . $currentSize . ")");
        }
        $this->dbDumpIterator->rewind();

        $tables = DUPX_DB_Tables::getInstance()->getTables();
        Log::info("--------------------------------------");
        Log::info('TABLES');
        Log::info("--------------------------------------");
        foreach ($tables as $tablesObj) {
            Log::info('TABLE ' . str_pad(Log::v2str($tablesObj->getOriginalName()), 50, '_', STR_PAD_RIGHT)
                . '[ROWS:' . str_pad((string) $tablesObj->getRows(), 8, " ", STR_PAD_LEFT) . ']'
                . ' [' . ($tablesObj->extract() ? 'EXTRACT' : 'NO EXTR') . '|' . ($tablesObj->replaceEngine() ? 'REPLACE' : 'NO REPL') . '] '
                . '[INST NAME: ' . $tablesObj->getNewName() . ']');
        }
        Log::info("********************************************************************************\n");
        Log::flush();
    }

    /**
     * Deploy
     *
     * @return mixed[]
     */
    public function deploy()
    {
        $paramsManager = PrmMng::getInstance();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        Log::setThrowExceptionOnError(true);
        if ($this->firstOrNotChunking()) {
            if ($this->post['dbchunk_retry'] > 0) {
                Log::info("## >> Last DB Chunk installation was failed, so retrying from start point. Retrying count: " . $this->post['dbchunk_retry']);
            }

            $this->prepareCpanel();
            $this->prepareDB();

            //Fatal Memory errors from file_get_contents is not catchable.
            //Try to warn ahead of time with a check on buffer in memory difference
            $current_php_mem = SnapUtil::convertToBytes($GLOBALS['PHP_MEMORY_LIMIT']);

            if ($current_php_mem >= 0 && $this->dbDumpIterator->currentSize() > $current_php_mem) {
                $readable_size = DUPX_U::readableByteSize($this->dbDumpIterator->currentSize());
                $msg           = "\nWARNING: The database script is '{$readable_size}' in size.  The PHP memory allocation is set\n";
                $msg          .= "at '{$GLOBALS['PHP_MEMORY_LIMIT']}'.  There is a high possibility that the installer script will fail with\n";
                $msg          .= "a memory allocation error when trying to load the database.sql file.  It is\n";
                $msg          .= "recommended to increase the 'memory_limit' setting in the php.ini config file.\n";
                $msg          .= "see: " . DUPX_Constants::FAQ_URL . "how-to-manage-server-resources-cpu-memory-disk/ \n";
                Log::info($msg);
                unset($msg);
            }

            Log::info("--------------------------------------");
            Log::info("DATABASE RESULTS");
            Log::info("--------------------------------------");
        }

        switch ($paramsManager->getValue(PrmMng::PARAM_DB_ACTION)) {
            case self::DBACTION_DO_NOTHING:
                Log::info("\n** SQL EXECUTION IS BEING SKIPPED **");
                Log::info("- The database was excluded during build -");
                $this->post['pass']              = 1;
                $this->post['continue_chunking'] = false;
                break;
            case self::DBACTION_MANUAL:
                Log::info("\n** SQL EXECUTION IS IN MANUAL MODE **");
                Log::info("- No SQL script has been executed -");
                $this->post['pass']              = 1;
                $this->post['continue_chunking'] = false;
                break;
            case DUPX_DBInstall::DBACTION_ONLY_CONNECT:
            case DUPX_DBInstall::DBACTION_CREATE:
            case DUPX_DBInstall::DBACTION_EMPTY:
            case DUPX_DBInstall::DBACTION_REMOVE_ONLY_TABLES:
            case DUPX_DBInstall::DBACTION_RENAME:
                if ($this->firstOrNotChunking()) {
                    $this->beforeInstallDatabaseActions();
                }
                $this->insertDatabase();
                if (!$this->post['continue_chunking']) {
                    $this->afterInstallDatabaseActions();
                }
                break;
            default:
                throw new Exception('Invalid db action');
        }
        $this->post['first_chunk'] = false;

        $this->saveData();
        $nManager->saveNotices();

        return $this->getResultData();
    }


    /**
     * Insert database
     *
     * @return void
     */
    protected function insertDatabase()
    {
        $paramsManager = PrmMng::getInstance();
        $validation    = false;
        if ($paramsManager->getValue(PrmMng::PARAM_DB_CHUNK)) {
            if ($this->post['continue_chunking'] == true) {
                if ($this->deployDatabaseChunkMode() == false) {
                    throw new Exception('Error on db extraction');
                }
            } elseif ($this->post['pass'] == 1) {
                $validation = true;
            } else {
                throw new Exception('Error on db extraction');
            }
        } else {
            $this->deployDatabaseSingleMode();
            $validation = true;
        }

        if ($validation) {
            $rowCountMisMatchTables = $this->getRowCountMisMatchTables();
            $this->post['pass']     = 1;
            if (!empty($rowCountMisMatchTables)) {
                $nManager = DUPX_NOTICE_MANAGER::getInstance();
                $errMsg   = 'Database Table row count verification was failed for table(s): '
                    . implode(', ', $rowCountMisMatchTables) . '.';
                Log::info($errMsg);
                $nManager->addBothNextAndFinalReportNotice(
                    array(
                        'shortMsg' => 'Database Table row count was validation failed',
                        'level'    => DUPX_NOTICE_ITEM::NOTICE,
                        'longMsg'  => $errMsg,
                        'sections' => 'database',
                    )
                );
            }
        }
    }

    /**
     * Actione executed before db install
     *
     * @return void
     */
    protected function beforeInstallDatabaseActions()
    {
        $this->queryFixes = new QueryFixes();
        $this->queryFixes->logRules();
        DbUserMode::moveTargetUserTablesOnCurrentPrefix();
        $this->dbUserMode->removeAllUserMetaKeysOfCurrentPrefix();
        $this->dbUserMode->initTargetSiteUsersData();
        $this->saveData();
    }

    /**
     * After install database actions
     *
     * @return void
     */
    protected function afterInstallDatabaseActions()
    {
        $this->dbUserMode->generateImportReport();
        $profileEnd = DUPX_U::getMicrotime();
        $this->writeLog();

        //FINAL RESULTS
        $ajax1_sum = DUPX_U::elapsedTime($profileEnd, $this->start_microtime);
        Log::info("\nINSERT DATA RUNTIME: " . DUPX_U::elapsedTime($profileEnd, $this->profile_start));
        Log::info('STEP-2 COMPLETE @ ' . @date('h:i:s') . " - RUNTIME: {$ajax1_sum}");
        self::resetData();
    }

    /**
     * Prepare cpanel
     *
     * @return void
     */
    protected function prepareCpanel()
    {
        if ($this->dbaction === self::DBACTION_MANUAL || InstState::dbDoNothing()) {
            return;
        }

        if ($this->post['view_mode'] != 'cpnl') {
            return;
        }

        try {
            //===============================================
            //CPANEL LOGIC: From Postback
            //===============================================

            $cpnllog  = "";
            $cpnllog .= "--------------------------------------\n";
            $cpnllog .= "CPANEL API\n";
            $cpnllog .= "--------------------------------------\n";

            $cpnlApiErr = 'The cPanel API had the following issues when trying to communicate on this host: <br/> %s';

            $CPNL = new DUPX_cPanel_Controller();

            $cpnlToken = $CPNL->create_token($this->post['cpnl-host'], $this->post['cpnl-user'], $this->post['cpnl-pass']);
            $cpnlHost  = $CPNL->connect($cpnlToken);

            //CREATE DB USER: Attempt to create user should happen first in the case that the
            //user passwords requirements are not met.
            if ($this->post['cpnl-dbuser-chk']) {
                $result = $CPNL->create_db_user($cpnlToken, $this->post['dbuser'], $this->post['dbpass']);
                if ($result['status'] !== true) {
                    Log::info('CPANEL API ERROR: create_db_user ' . print_r($result['cpnl_api'], true), 2);
                    Log::error(sprintf($cpnlApiErr, $result['status']));
                } else {
                    $cpnllog .= "- A new database user was created\n";
                }
            }

            //CREATE NEW DB
            if ($this->dbaction == self::DBACTION_CREATE) {
                $result = $CPNL->create_db($cpnlToken, $this->post['dbname']);
                if ($result['status'] !== true) {
                    Log::info('CPANEL API ERROR: create_db ' . print_r($result['cpnl_api'], true), 2);
                    Log::error(sprintf($cpnlApiErr, $result['status']));
                } else {
                    $cpnllog .= "- A new database was created\n";
                }
            } else {
                $cpnllog .= "- Used to connect to existing database named [" . $this->post['dbname'] . "]\n";
            }

            //ASSIGN USER TO DB IF NOT ASSIGNED
            $result = $CPNL->is_user_in_db($cpnlToken, $this->post['dbname'], $this->post['dbuser']);
            if (!$result['status']) {
                $result = $CPNL->assign_db_user($cpnlToken, $this->post['dbname'], $this->post['dbuser']);
                if ($result['status'] !== true) {
                    Log::info('CPANEL API ERROR: assign_db_user ' . print_r($result['cpnl_api'], true), 2);
                    Log::error(sprintf($cpnlApiErr, $result['status']));
                } else {
                    $cpnllog .= "- Database user was assigned to database";
                }
            }

            Log::info($cpnllog);
        } catch (Exception $ex) {
            Log::error($ex);
        }
    }

    /**
     *
     * @return string
     */
    protected static function dbinstallDataFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_INST_DB_DATA);
        }
        return $path;
    }

    /**
     * Seek tell log file path
     *
     * @return string
     */
    protected static function seekTellFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_INST_DB_SEEK_TELL_LOG);
        }
        return $path;
    }

    /**
     * Save data to file
     *
     * @return bool
     */
    protected function saveData()
    {
        if (($json = SnapJson::jsonEncodePPrint($this)) === false) {
            Log::info('Can\'t encode json data');
            return false;
        }

        if (file_put_contents(self::dbinstallDataFilePath(), $json) === false) {
            throw new Exception('Can\'t save dbinstall data file');
        }

        return true;
    }

    /**
     * It can clean up the object and is supposed to return an array with the
     * names of all variables of that object that should be serialized.
     *
     * @return string[]
     */
    public function __sleep()
    {
        $props = array_keys(get_object_vars($this));
        return array_diff($props, array('dbh'));
    }

    /**
     * Load data from file
     *
     * @return boolean
     */
    protected function loadData()
    {
        if (!file_exists(self::dbinstallDataFilePath())) {
            return false;
        }

        if (($json = file_get_contents(self::dbinstallDataFilePath())) === false) {
            throw new Exception('Can\'t load dbinstall data file');
        }

        JsonSerialize::unserializeToObj($json, $this);

        return true;
    }

    /**
     * Reset all data
     *
     * @return boolean
     */
    public static function resetData()
    {
        $result = true;
        if (file_exists(self::dbinstallDataFilePath())) {
            if (unlink(self::dbinstallDataFilePath()) === false) {
                throw new Exception('Can\'t delete dbinstall data file');
            }
        }

        self::resetSeekTellData();

        return $result;
    }

    /**
     * Reset seek tell data
     *
     * @return boolean
     */
    public static function resetSeekTellData()
    {
        if (file_exists(self::seekTellFilePath())) {
            if (unlink(self::seekTellFilePath()) === false) {
                throw new Exception('Can\'t delete dbinstall chunk seek data file');
            }
        }

        return true;
    }

    /**
     * Reset seek tell data
     *
     * @param int $offset The offset in the current file
     *
     * @return boolean
     */
    private function appendSeekTellData($offset)
    {
        $logLine = $this->post['pos'] . '-' . $offset;
        if (file_exists(self::seekTellFilePath()) && filesize(self::seekTellFilePath()) > 0) {
            $logLine = ',' . $logLine;
        }

        return file_put_contents(self::seekTellFilePath(), $logLine, FILE_APPEND) !== false;
    }

    /**
     * Execute a connection if db isn't connected
     *
     * @param bool $reconnect if true force a new connection
     *
     * @return ?mysqli
     */
    protected function dbConnect($reconnect = false)
    {
        if ($reconnect) {
            $this->dbClose();
        }

        $paramsManager = PrmMng::getInstance();

        if (is_null($this->dbh)) {
            switch ($this->dbaction) {
                case self::DBACTION_EMPTY:
                case self::DBACTION_REMOVE_ONLY_TABLES:
                case self::DBACTION_RENAME:
                case self::DBACTION_ONLY_CONNECT:
                    //ESTABLISH CONNECTION
                    if (($this->dbh = DUPX_DB_Functions::getInstance()->dbConnection()) == false) {
                        $this->dbh = null;
                        Log::error('DATABASE CONNECTION FAILED!<br/>' . mysqli_connect_error());
                    }

                    // EXEC ALWAYS A DB SELECT is required when chunking is activated
                    if (DUPX_DB::selectDB($this->dbh, $paramsManager->getValue(PrmMng::PARAM_DB_NAME)) == false) {
                        Log::error(
                            sprintf(
                                'The database "%s" does not exist.<br/>  Change the action to create in order to "Create New Database" to ' .
                                'create the database.  Some hosting providers do not allow database creation except through their control panels. ' .
                                'In this case, you will need to login to your hosting providers control panel and create the database manually. ' .
                                'Please contact your hosting provider for further details on how to create the database.',
                                $paramsManager->getValue(PrmMng::PARAM_DB_NAME)
                            )
                        );
                    }
                    break;
                case self::DBACTION_CREATE:
                    //ESTABLISH CONNECTION WITHOUT DATABASE NAME
                    $connParams = array(
                        'dbhost' => $paramsManager->getValue(PrmMng::PARAM_DB_HOST),
                        'dbname' => null,
                        'dbuser' => $paramsManager->getValue(PrmMng::PARAM_DB_USER),
                        'dbpass' => $paramsManager->getValue(PrmMng::PARAM_DB_PASS),
                    );

                    if (($this->dbh = DUPX_DB_Functions::getInstance()->dbConnection($connParams)) == false) {
                        $this->dbh = null;
                        Log::error('DATABASE CONNECTION FAILED!<br/>' . mysqli_connect_error());
                    }

                    // don't check for success because in the create new database option the database may not exist.
                    DUPX_DB::selectDB($this->dbh, $paramsManager->getValue(PrmMng::PARAM_DB_NAME));
                    break;
                case self::DBACTION_DO_NOTHING:
                    Log::info('DB ACTION DO NOTHING');
                    break;
                case self::DBACTION_MANUAL:
                    Log::info('DB ACTION MANUAL');
                    break;
                default:
                    Log::error('Invalid dbaction: ' . Log::v2str($this->dbaction));
                    break;
            }

            try {
                DUPX_DB::mysqli_query($this->dbh, "SET wait_timeout = " . mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_TIME']));
                DUPX_DB::mysqli_query($this->dbh, "SET GLOBAL max_allowed_packet = " . mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_PACKETS']), Log::LV_DEBUG);
                DUPX_DB::mysqli_query($this->dbh, "SET max_allowed_packet = " . mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_PACKETS']), Log::LV_DEBUG);

                $this->dbvar_maxtime  = DUPX_DB::getVariable($this->dbh, 'wait_timeout', 300);
                $this->dbvar_maxpacks = DUPX_DB::getVariable($this->dbh, 'max_allowed_packet', MB_IN_BYTES);
                $this->dbvar_sqlmode  = DUPX_DB::getVariable($this->dbh, 'sql_mode', 'NOT_SET');
            } catch (Exception $e) {
                Log::logException($e, Log::LV_DEFAULT, 'EXCEPTION ON DB SET VARS [CONTINUE]');
            }
        }
        return $this->dbh;
    }

    /**
     * Close the database connection
     *
     * @return void
     */
    protected function dbClose()
    {
        if (!is_null($this->dbh)) {
            mysqli_close($this->dbh);
            $this->dbh = null;
        }
    }

    /**
     * Pings the database and reconnects if the connection is lost
     *
     * @return void
     */
    protected function pingAndReconnect()
    {
        if (!mysqli_ping($this->dbh)) {
            $this->dbConnect(true);
        }
    }

    /**
     * Prepare the database for the install
     *
     * @return void
     */
    protected function prepareDB()
    {
        if ($this->dbaction === self::DBACTION_MANUAL || InstState::dbDoNothing()) {
            return;
        }

        $this->dbConnect();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        DUPX_DB::setCharset($this->dbh, $this->dbcharset, $this->dbcollate);
        $this->setSQLSessionMode();

        //Set defaults incase the variable could not be read
        $this->drop_tbl_log   = 0;
        $this->rename_tbl_log = 0;

        Log::info("--------------------------------------");
        Log::info('DATABASE-ENVIRONMENT');
        Log::info("--------------------------------------");
        Log::info(
            "MYSQL VERSION:\tThis Server: " .
            DUPX_DB::getVersion($this->dbh) .
            " -- Build Server: {$archiveConfig->version_db}"
        );
        Log::info("TIMEOUT:\t{$this->dbvar_maxtime}");
        Log::info("MAXPACK:\t{$this->dbvar_maxpacks}");
        Log::info("SQLMODE-GLOBAL:\t{$this->dbvar_sqlmode}");
        Log::info("SQLMODE-SESSION:" . ($this->getSQLSessionMode()));

        switch ($this->dbaction) {
            case self::DBACTION_CREATE:
                $this->dbActionCreate();
                break;
            case self::DBACTION_EMPTY:
                $this->dbActionEmpty();
                break;
            case self::DBACTION_REMOVE_ONLY_TABLES:
                $this->dbActionRemoveOnlyTables();
                break;
            case self::DBACTION_RENAME:
                $this->dbActionRename();
                break;
            case self::DBACTION_DO_NOTHING:
            case self::DBACTION_MANUAL:
            case self::DBACTION_ONLY_CONNECT:
                break;
            default:
                Log::error('DB ACTION INVALID');
                break;
        }
    }

    /**
     * DBACTION_CREATE
     *
     * @return void
     */
    protected function dbActionCreate()
    {
        if ($this->post['view_mode'] == 'basic') {
            DUPX_DB::mysqli_query($this->dbh, "CREATE DATABASE IF NOT EXISTS `" . mysqli_real_escape_string($this->dbh, $this->post['dbname']) . "`");
        }

        if (mysqli_select_db($this->dbh, mysqli_real_escape_string($this->dbh, $this->post['dbname'])) == false) {
            Log::error(
                sprintf(
                    'DATABASE CREATION FAILURE!<br/> Unable to create database "%s". ' .
                    'Check to make sure the user has "Create" privileges.  Some hosts will restrict the creation of a database only through the cpanel. ' .
                    'Try creating the database manually to proceed with the installation.  If the database already exists select the action ' .
                    '"Connect and Remove All Data" which will remove all existing tables.',
                    $this->post['dbname']
                )
            );
        }
    }

    /**
     * DB action empty
     *
     * @return void
     */
    protected function dbActionEmpty()
    {
        $excludeDropTable = DUPX_DB_Functions::getExcludedTables();

        if (InstState::isBridgeInstall()) {
            Log::info('EXCLUDE OPTION TABLE TO REMOVE');
            $excludeDropTable[] = DUPX_DB_Functions::getOptionsTableName();
            DUPX_DB::emptyTable($this->dbh, DUPX_DB_Functions::getOptionsTableName());
            DbUtils::updateWpOption($this->dbh, 'siteurl', PrmMng::getInstance()->getValue(PrmMng::PARAM_SITE_URL));
            DbUtils::updateWpOption($this->dbh, 'home', PrmMng::getInstance()->getValue(PrmMng::PARAM_URL_NEW));
        }

        if ($this->restoreBackupPackagesPreAction()) {
            $excludeDropTable[] = DUPX_DB_Functions::getPackagesTableName();
        }

        //Drop all tables, views and procs
        $this->dropTables($excludeDropTable);
        DbCleanup::dropViews();
        DbCleanup::dropProcs();
        DbCleanup::dropFuncs();
    }

    /**
     * DB action remove only tables
     *
     * @return void
     */
    protected function dbActionRemoveOnlyTables()
    {
        $excludeDropTable = DUPX_DB_Functions::getExcludedTables();

        if ($this->restoreBackupPackagesPreAction()) {
            $excludeDropTable[] = DUPX_DB_Functions::getPackagesTableName();
        }

        $this->dropTables($excludeDropTable, DUPX_DB_Tables::getInstance()->getNewTablesNames());

        if (!InstState::isAddSiteOnMultisite()) {
            DbCleanup::dropProcs();
            DbCleanup::dropFuncs();
            DbCleanup::dropViews();
        }
    }


    /**
     * Restore backup packages pre action
     *
     * @return bool Return true if restore backup pre action is required otherwise false
     */
    protected function restoreBackupPackagesPreAction()
    {
        if (!InstState::isRestoreBackup()) {
            return false;
        }

        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        if (!$overwriteData['packagesTableExists']) {
            return false;
        }
        return true;
    }

    /**
     * Db action rename
     *
     * @return void
     */
    protected function dbActionRename()
    {
        Log::info('TABLE RENAME TO BACKUP');

        $copyTables = array();
        if (ParamDescUsers::getUsersMode() !== ParamDescUsers::USER_MODE_OVERWRITE) {
            $paramsManager = PrmMng::getInstance();
            $overwriteData = $paramsManager->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
            $copyTables    = array(
                DUPX_DB_Functions::getUserTableName($overwriteData['table_prefix']),
                DUPX_DB_Functions::getUserMetaTableName($overwriteData['table_prefix']),
            );
        }

        DUPX_DB_Functions::getInstance()->pregReplaceTableName('/^(.+)$/', $GLOBALS['DB_RENAME_PREFIX'] . '$1', array(
            'prefixFilter'         => DUPX_Constants::BACKUP_RENAME_PREFIX,
            'regexTablesDropFkeys' => '^' . SnapDB::quoteRegex($GLOBALS['DB_RENAME_PREFIX']) . '.+',
            'copyTables'           => $copyTables,
            'exclude'              => array(
                DUPX_DB_Functions::getUserTableName(self::TEMP_DB_PREFIX),
                DUPX_DB_Functions::getUserMetaTableName(self::TEMP_DB_PREFIX),
            ),
        ));
    }

    /**
     * Return true if is delimiter query line and set new delimiter
     *
     * @param string $line query line
     *
     * @return boolean|string false if isn't delimiter or delimiter string
     */
    protected static function isDelimiterLine($line)
    {
        $delimiterMatch = null;

        if (preg_match('/^\s*DELIMITER\s+([^\s]+)\s*$/i', $line, $delimiterMatch) === 1) {
            $delimiter = $delimiterMatch[1];
            Log::info("SET DELIMITER " . $delimiter . " AND SKIP QUERY");
            return $delimiter;
        } else {
            return false;
        }
    }

    /**
     * Deploy database in chunk mode
     *
     * @return boolean
     */
    protected function deployDatabaseChunkMode()
    {
        Log::info("--------------------------------------");
        Log::info("** DATABASE CHUNK install start");
        Log::info("--------------------------------------");
        $this->dbConnect();

        if (isset($this->post['dbchunk_retry']) && $this->post['dbchunk_retry'] > 0) {
            Log::info("DATABASE CHUNK RETRY COUNT: " . Log::v2str($this->post['dbchunk_retry']));
        }

        $delimiter    = $this->post['delimiter'];
        $query_offset = 0;

        if (!$this->dbDumpIterator->valid()) {
            Log::info('DATABASE CHUNK: No more sql files to process');
            return false;
        }

        $sqlFilePath = $this->dbDumpIterator->current();
        $handle      = fopen($sqlFilePath, 'rb');
        if ($handle === false) {
            return false;
        }

        Log::info("PROCESSING SQL FILE " . basename($sqlFilePath) . " (" . ($this->dbDumpIterator->key() + 1) .
            " of " . $this->dbDumpIterator->count() . ")");
        Log::info("OFFSET " . Log::v2str($this->post['pos']) . " OF " . $this->dbDumpIterator->currentSize());

        if (-1 !== fseek($handle, $this->post['pos'])) {
            DUPX_DB::setCharset($this->dbh, $this->dbcharset, $this->dbcollate);

            $this->setSQLSessionMode();

            $this->thread_start_time = DUPX_U::getMicrotime();

            Log::info('DATABASE CHUNK START FILE: ' . basename($sqlFilePath) . ' POS:' . Log::v2str($this->post['pos']), Log::LV_DETAILED);
            $this->pingAndReconnect();

            if (@mysqli_autocommit($this->dbh, false)) {
                Log::info('Auto Commit set to false successfully');
            } else {
                Log::info('Failed to set Auto Commit to false');
            }

            Log::info("DATABASE CHUNK: Iterating query loop", Log::LV_DEBUG);

            if (!$this->post['first_chunk'] && !empty($this->setQueries)) {
                Log::info("SET QUERIES FROM FIRST CHUNK", Log::LV_DETAILED);
                foreach ($this->setQueries as $setQuery) {
                    Log::info("\tSET QUERY " . Log::v2str($setQuery), Log::LV_DEBUG);
                    $this->writeQueryInDB($setQuery);
                }
            }

            $query                 = '';
            $skipChunkTimeoutCheck = $this->dbsplit_creates && $this->post['first_chunk'];

            while (($line = fgets($handle)) !== false) {
                if (($res = self::isDelimiterLine($line)) !== false) {
                    $query     = '';
                    $delimiter = $this->post['delimiter'] = $res;
                    continue;
                }

                if ($this->post['first_chunk']) {
                    //Matches ordinary set queries e.g "SET @saved_cs_client = @@character_set_client;"
                    //and version dependent set queries e.g. "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;"
                    if (preg_match('/^[\s\t]*(?:\/\*!\d+)?[\s\t]*SET[\s\t]*@.+;/', $line)) {
                        $setQuery = trim($line);
                        if (!in_array($setQuery, $this->setQueries)) {
                            Log::info("FIRST CHUNK SET QUERY " . Log::v2str($setQuery), Log::LV_DEBUG);
                            $this->setQueries[] = $setQuery;
                        }
                    }

                    if ($line === self::TABLE_CREATION_END_MARKER) {
                        Log::info("DATABASE CHUNK: CREATION TABLE MARKER FOUND");
                        $skipChunkTimeoutCheck = false;
                        continue;
                    }
                }

                $query .= $line;
                if (preg_match('/' . preg_quote($delimiter, '/') . '\s*$/S', $line)) {
                    // Temp: Uncomment this to randomly kill the php db process to simulate real world hosts and verify system recovers properly
                    /*
                      $rand_no = rand(0, 500);
                      if (0 == $this->post['dbchunk_retry'] && 1 == $rand_no) {
                      Log::info("intentionally killing db chunk installation process");
                      error_log('intentionally killing db chunk installation process');
                      exit(1);
                      }
                     */

                    $this->writeQueryInDB($query);
                    $query = '';

                    $elapsed_time = (microtime(true) - $this->thread_start_time);
                    if (Log::isLevel(Log::LV_DEBUG)) {
                        Log::info("DATABASE CHUNK: Elapsed time: " . Log::v2str($elapsed_time), Log::LV_HARD_DEBUG);
                        if ($elapsed_time > DUPX_Constants::CHUNK_DBINSTALL_TIMEOUT_TIME) {
                            Log::info("DATABASE CHUNK: Breaking query loop.", Log::LV_DEBUG);
                        } else {
                            Log::info("DATABASE CHUNK: Not Breaking query loop", Log::LV_HARD_DEBUG);
                        }
                    }

                    //Only stop first chunk if all CREATE queries have been run
                    if (!$skipChunkTimeoutCheck && $elapsed_time > DUPX_Constants::CHUNK_DBINSTALL_TIMEOUT_TIME) {
                        break;
                    }
                }
            }

            if (@mysqli_autocommit($this->dbh, true)) {
                Log::info('Auto Commit set to true successfully');
            } else {
                Log::info('Failed to set Auto Commit to true');
            }

            $query_offset = ftell($handle);
            $this->appendSeekTellData($query_offset);

            $this->post['progress'] = ceil($this->dbDumpIterator->totalOffset($query_offset) / $this->dbDumpIterator->totalSize() * 100);
            $this->post['pos']      = $query_offset;

            if (feof($handle)) {
                if ($this->seekIntegrityCheck()) {
                    Log::info('DATABASE CHUNK: DB install chunk process integrity check has been just passed successfully.', Log::LV_DETAILED);
                    $this->dbDumpIterator->next();
                    if ($this->dbDumpIterator->valid()) {
                        $this->post['pos']               = 0;
                        $this->post['pass']              = 0;
                        $this->post['continue_chunking'] = true;
                        self::resetSeekTellData();
                        Log::info("FINISHED PROCESSING " . basename($sqlFilePath));
                        Log::info("NEXT " . basename($this->dbDumpIterator->current()));
                    } else {
                        Log::info("ALL SQL FILES PROCESSED");
                        $this->post['pass']              = 1;
                        $this->post['continue_chunking'] = false;
                    }
                } else {
                    Log::info('DB install chunk process integrity check has been just failed.');
                    $this->post['pass']      = 0;
                    $this->post['is_error']  = 1;
                    $this->post['error_msg'] = 'DB install chunk process integrity check has been just failed.';
                }
            } else {
                $this->post['pass']              = 0;
                $this->post['continue_chunking'] = true;
            }
        }
        Log::info("DATABASE CHUNK: End Query offset " . Log::v2str($query_offset), Log::LV_DETAILED);

        if ($this->post['pass']) {
            Log::info('DATABASE CHUNK: This is last chunk', Log::LV_DETAILED);
        }

        fclose($handle);

        Log::info("--------------------------------------");
        Log::info("** DATABASE CHUNK install end");
        Log::info("--------------------------------------");

        ob_flush();
        flush();
        return true;
    }

    /**
     * Seek integrity check
     *
     * @return bool
     */
    protected function seekIntegrityCheck()
    {
        // ensure integrity
        $seek_tell_log          = file_get_contents(self::seekTellFilePath());
        $seek_tell_log_explodes = explode(',', $seek_tell_log);
        $last_start             = 0;
        $last_end               = 0;
        foreach ($seek_tell_log_explodes as $seek_tell_log_explode) {
            $temp_arr = explode('-', $seek_tell_log_explode);
            if (is_array($temp_arr) && 2 == count($temp_arr)) {
                $start = $temp_arr[0];
                $end   = $temp_arr[1];
                if ($start != $last_end) {
                    return false;
                }
                if ($last_start > $end) {
                    return false;
                }

                $last_start = $start;
                $last_end   = $end;
            } else {
                return false;
            }
        }

        if ($last_end != $this->dbDumpIterator->currentSize()) {
            return false;
        }
        return true;
    }

    /**
     * Check if query should be skipped
     *
     * @param string $query query to check
     *
     * @return bool return true if query should be skipped
     */
    protected static function skipQuery($query)
    {
        static $skipRegex = null;

        if (is_null($skipRegex)) {
            $skipRegex  = array();
            $skipTables = DUPX_DB_Tables::getInstance()->getTablesToSkip();
            $skipCreate = DUPX_DB_Tables::getInstance()->getTablesCreateSkip();

            if (count($skipTables) > 0) {
                $skipTables = array_map(function ($table) {
                    return preg_quote($table, '/');
                }, $skipTables);

                for ($i = 0; $i < ceil(count($skipTables) / self::TABLES_REGEX_CHUNK_SIZE); $i++) {
                    $subArray = array_slice($skipTables, $i * self::TABLES_REGEX_CHUNK_SIZE, self::TABLES_REGEX_CHUNK_SIZE);

                    if (count($subArray) == 0) {
                        break;
                    }

                    if (DUPX_ArchiveConfig::getInstance()->dbInfo->buildMode === self::BUILD_MODE_MYSQLDUMP) {
                        $skipRegex[] = '/^\s*(?:\/\*!\d+\s)?\s*(?:CREATE|INSERT|ALTER|LOCK)\s.*(?:TABLE|INTO).*[`\s](?-i)(' .
                            implode('|', $subArray) . ')(?i)[`\s]/im';
                    } else {
                        $skipRegex[] = '/^\s*(?:CREATE|INSERT)\s.*(?:TABLE|INTO).*[`\s](?-i)(' . implode('|', $subArray) . ')(?i)[`\s]/im';
                    }
                }
            }

            if (count($skipCreate) > 0) {
                $skipCreate = array_map(function ($table) {
                    return preg_quote($table, '/');
                }, $skipCreate);

                for ($i = 0; $i < ceil(count($skipCreate) / self::TABLES_REGEX_CHUNK_SIZE); $i++) {
                    $subArray = array_slice($skipCreate, $i * self::TABLES_REGEX_CHUNK_SIZE, self::TABLES_REGEX_CHUNK_SIZE);

                    if (count($subArray) == 0) {
                        break;
                    }

                    $skipRegex[] = '/^\s*CREATE\s.*TABLE.*[`\s](?-i)(' . implode('|', $subArray) . ')(?i)[`\s]/im';
                }
            }

            switch (count($skipRegex)) {
                case 0:
                    $skipRegex = false;
                    Log::info('NO TABLE TO SKIP');
                    break;
                case 1:
                    $skipRegex = $skipRegex[0];
                    // no break
                default:
                    Log::info(
                        'TABLES TO SKIP FOUND ' . Log::v2str(
                            array(
                                'Extraction'  => $skipTables,
                                'Create only' => $skipCreate,
                            )
                        ) . "\n"
                    );
                    Log::info('SKIP TABLE EXTRACTION REGEX ' . Log::v2str($skipRegex), Log::LV_DETAILED);
                    break;
            }
        }

        if (strlen($query) == 0) {
            return true;
        } elseif ($skipRegex === false) {
            return false;
        } elseif (is_array($skipRegex)) {
            foreach ($skipRegex as $regex) {
                if (preg_match($regex, $query) === 1) {
                    return true;
                }
            }
            return false;
        } else {
            return (preg_match($skipRegex, $query) === 1);
        }
    }

    /**
     * Get the tables that have a row count mismatch
     *
     * @return false|string[]
     */
    protected function getRowCountMisMatchTables()
    {
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $this->dbConnect();

        if (is_null($this->dbh)) {
            $errorMsg = "**ERROR** database DBH is null";
            $this->dbquery_errs++;
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg' => $errorMsg,
                'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                'sections' => 'database',
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            Log::info($errorMsg);
            $nManager->saveNotices();
            return false;
        }

        $tablesList     = $archiveConfig->dbInfo->tablesList;
        $tablePrefix    = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        $skipTables     = array(
            $tablePrefix . "duplicator_packages",
            DUPX_DB_Functions::getOptionsTableName(),
            DUPX_DB_Functions::getPackagesTableName(),
            DUPX_DB_Functions::getEntitiesTableName(),
        );
        $misMatchTables = array();
        foreach ($tablesList as $table => $tableInfo) {
            if ($tableInfo->insertedRows === false) {
                // if it is false it means that no precise count is available to perform the validity test.
                continue;
            }
            $table = $archiveConfig->getTableWithNewPrefix($table);
            if (in_array($table, $skipTables)) {
                continue;
            }
            $sql    = "SELECT count(*) as cnt FROM `" . mysqli_real_escape_string($this->dbh, $table) . "`";
            $result = DUPX_DB::mysqli_query($this->dbh, $sql);
            if (false !== $result) {
                $row = mysqli_fetch_assoc($result);
                if ($tableInfo->insertedRows != ($row['cnt'])) {
                    $errMsg = 'DATABASE: table ' . Log::v2str($table) . ' row count mismatch; expected ' . Log::v2str($tableInfo->insertedRows) . ' in database' . Log::v2str($row['cnt']);
                    Log::info($errMsg);
                    $nManager->addBothNextAndFinalReportNotice(array(
                        'shortMsg' => 'Database Table row count validation was failed',
                        'level'    => DUPX_NOTICE_ITEM::NOTICE,
                        'longMsg'  => $errMsg . "\n",
                        'sections' => 'database',
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'row-count-mismatch');
                    $misMatchTables[] = $table;
                }
            }
        }
        return $misMatchTables;
    }

    /**
     * Deploys the database in single mode
     *
     * @return void
     */
    protected function deployDatabaseSingleMode()
    {
        Log::info("--------------------------------------");
        Log::info("** DATABASE SNGLE MODE install start");
        Log::info("--------------------------------------");
        $this->dbConnect();

        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        if (is_null($this->dbh)) {
            $errorMsg = "**ERROR** database DBH is null";
            $this->dbquery_errs++;
            $nManager->addNextStepNoticeMessage($errorMsg, DUPX_NOTICE_ITEM::CRITICAL, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            $nManager->addFinalReportNotice(array(
                'shortMsg' => $errorMsg,
                'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                'sections' => 'database',
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            Log::info($errorMsg);
            $nManager->saveNotices();
            return;
        }

        $query     = '';
        $delimiter = ';';

        while ($this->dbDumpIterator->valid()) {
            $sqlFilePath = $this->dbDumpIterator->current();
            if (($handle = @fopen($sqlFilePath, 'rb')) === false) {
                return;
            }
            Log::info("PROCESSING SQL FILE " . basename($sqlFilePath) . " (" . ($this->dbDumpIterator->key() + 1) . " of "
                . $this->dbDumpIterator->count() . ")");

            while (($line = fgets($handle)) !== false) {
                if (($res = self::isDelimiterLine($line)) !== false) {
                    $query     = '';
                    $delimiter = $this->post['delimiter'] = $res;
                    continue;
                }

                $query .= $line;

                if (preg_match('/' . preg_quote($delimiter, '/') . '\s*$/S', $line)) {
                    $this->writeQueryInDB($query);
                    $query = '';
                }
            }

            @fclose($handle);
            $this->dbDumpIterator->next();
        }

        Log::info("ALL SQL FILES PROCESSED");
        $nManager->saveNotices();
    }

    /**
     * @param string $query query to write
     *
     * @return bool true if query was written successfully
     */
    protected function writeQueryInDB($query)
    {
        $query = trim($query);
        if ($this->skipQuery($query)) {
            return true;
        }

        $return   = false;
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        $query = $this->queryFixes->applyFixes($query);
        $query = $this->dbUserMode->applyUsersFixes($query);

        if (strlen($query) == 0) {
            return true;
        }

        if (($queryLen = strlen($query)) > $this->dbvar_maxpacks) {
            $errorMsg = "FAILED QUERY LIMIT [QLEN:" . $queryLen . "|MAX:{$this->dbvar_maxpacks}]\n\t[SQL=" . substr($query, 0, self::QUERY_ERROR_LOG_LEN) . "...]\n\n";
            $this->dbquery_errs++;
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg'    => 'Query size limit error (max limit ' . $this->dbvar_maxpacks . ')',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $errorMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'database',
                'faqLink'     => array(
                    'url'   => DUPX_Constants::FAQ_URL . 'how-to-fix-database-errors-or-general-warnings-on-the-install-report',
                    'label' => 'FAQ Link',
                ),
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'query-size-limit-msg');
            Log::info($errorMsg);
            $return = false;
        }

        @mysqli_autocommit($this->dbh, false);
        //Check to make sure the connection is alive
        if (($query_res = DUPX_DB::mysqli_query($this->dbh, $query)) === false) {
            $err    = mysqli_error($this->dbh);
            $errMsg = "DATABASE ERROR: '{$err}'\n\t[SQL=" . substr($query, 0, self::QUERY_ERROR_LOG_LEN) . "...]\n\n";

            if (DUPX_U::contains($err, 'Unknown collation')) {
                $nManager->addNextStepNotice(array(
                    'shortMsg'    => 'DATABASE ERROR: ' . $err,
                    'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at ' . DUPX_Constants::FAQ_URL . 'how-to-fix-database-write-issues',
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'faqLink'     => array(
                        'url'   => DUPX_Constants::FAQ_URL . 'how-to-fix-database-write-issues',
                        'label' => 'FAQ Link',
                    ),
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-collation-write-msg');
                $nManager->addFinalReportNotice(array(
                    'shortMsg'    => 'DATABASE ERROR: ' . $err,
                    'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                    'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at ' . DUPX_Constants::FAQ_URL . 'how-to-fix-database-write-issues' . '<br>' . $errMsg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections'    => 'database',
                    'faqLink'     => array(
                        'url'   => DUPX_Constants::FAQ_URL . 'how-to-fix-database-write-issues',
                        'label' => 'FAQ Link',
                    ),
                ));
                Log::info('RECOMMENDATION: Try resolutions found at ' . DUPX_Constants::FAQ_URL . 'how-to-fix-database-write-issues');
            } elseif (!$this->skipErrorNotice($err, $query)) {
                $nManager->addNextStepNotice(array(
                    'shortMsg'    => 'DATABASE ERROR: database error write',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => $errMsg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'query-write-msg');
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'DATABASE ERROR: ' . $err,
                    'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'  => $errMsg,
                    'sections' => 'database',
                ));
            }

            $this->pingAndReconnect();
            $this->dbquery_errs++;

            //Buffer data to browser to keep connection open
            $return = false;
        } else {
            if (!is_bool($query_res)) {
                @mysqli_free_result($query_res);
            }
            $this->dbquery_rows++;
            $return = true;
        }

        @mysqli_commit($this->dbh);
        @mysqli_autocommit($this->dbh, true);
        return $return;
    }

    /**
     *  SQL Session Mode
     *
     *  @return string
     */
    private function getSQLSessionMode()
    {
        $this->dbConnect();
        $result = DUPX_DB::mysqli_query($this->dbh, "SELECT @@SESSION.sql_mode;");
        $row    = mysqli_fetch_row($result);
        $result->close();
        return is_array($row) ? $row[0] : '';
    }

    /**
     * SQL MODE OVERVIEW:
     * sql_mode can cause db create issues on some systems because the mode affects how data is inserted.
     * Right now defaulting to  NO_AUTO_VALUE_ON_ZERO (https://dev.mysql.com/doc/refman/5.5/en/sql-mode.html#sqlmode_no_auto_value_on_zero)
     * has been the saftest option because the act of seting the sql_mode will nullify the MySQL Engine defaults which can be very problematic
     * if the default is something such as STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_DATE.  So the default behavior will be to always
     * use NO_AUTO_VALUE_ON_ZERO.  If the user insits on using the true system defaults they can use the Custom option.  Note these values can
     * be overriden by values set in the database.sql script such as:
     * !40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'
     *
     * @return void
     */
    private function setSQLSessionMode()
    {
        $this->dbConnect();
        switch ($this->post['dbmysqlmode']) {
            case 'DEFAULT':
                $query = "SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO'";
                break;
            case 'DISABLE':
                $query = "SET SESSION sql_mode = ''";
                break;
            case 'CUSTOM':
                $query = "SET SESSION sql_mode = '" . mysqli_real_escape_string($this->dbh, $this->post['dbmysqlmode_opts']) . "'";
                break;
            default:
                throw new Exception('Unknown dbmysqlmode option ' . $this->post['dbmysqlmode']);
        }

        if (!$result = DUPX_DB::mysqli_query($this->dbh, $query)) {
            $sql_error = mysqli_error($this->dbh);
            $long      = "WARNING: A custom sql_mode setting issue has been detected:\n{$sql_error}.<br>";
            $long     .= "The installation continue with the default MySQL Mode of the database.<br><br>";
            $long     .= "For more details visit: <a href=\"https://dev.mysql.com/doc/refman/8.0/en/sql-mode.html\" target=\"_blank\">sql-mode documentation</a>";
            DUPX_NOTICE_MANAGER::getInstance()->addBothNextAndFinalReportNotice(array(
                'shortMsg'    => 'SET SQL MODE ERROR',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $long,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'database',
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'drop-mysql-mode-set');
        }
    }

    /**
     * Drops tables in the database
     *
     * @param string[]      $exclude tables to exclude
     * @param bool|string[] $tables  if true drop all tables or table in list
     *
     * @return void
     */
    private function dropTables($exclude = array(), $tables = true)
    {
        $logMsg = 'DROP' . ($tables === true ? ' ALL TABLES' : ' TABLES ' . Log::v2str($tables));
        if (count($exclude) > 0) {
            $logMsg .= ' EXCEPT ' . Log::v2str($exclude);
        }
        Log::info($logMsg);

        $found_tables = array();

        $sql = "SHOW FULL TABLES WHERE Table_Type != 'VIEW'";
        if (($result = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            Log::error('QUERY ' . Log::v2str($sql) . 'ERROR: ' . mysqli_error($this->dbh));
        }
        while ($row = mysqli_fetch_row($result)) {
            if (in_array($row[0], $exclude)) {
                continue;
            }

            if (is_bool($tables) && $tables == false) {
                continue;
            }

            if (is_array($tables) && !in_array($row[0], $tables)) {
                continue;
            }

            $found_tables[] = $row[0];
        }

        if (!count($found_tables)) {
            return;
        }

        DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 0;");
        foreach ($found_tables as $table_name) {
            //Log::info('DROP TABLE ' . $table_name, Log::LV_DEBUG);
            Log::info('DROP TABLE ' . $table_name);
            $sql = "DROP TABLE `" . mysqli_real_escape_string($this->dbh, $this->post['dbname']) . "`.`" . mysqli_real_escape_string($this->dbh, $table_name) . "`";
            if (!$result = DUPX_DB::mysqli_query($this->dbh, $sql)) {
                Log::error(
                    sprintf(
                        'TABLE CLEAN FAILURE' .
                        'Unable to remove TABLE "%s" from database "%s".<br/>'  .
                        'Please remove all tables from this database and try the installation again. ' .
                        'If no tables show in the database, then Drop the database and re-create it.<br/>' .
                        'ERROR MESSAGE: %s',
                        $table_name,
                        $this->post['dbname'],
                        mysqli_error($this->dbh)
                    )
                );
            }
        }
        DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 1;");

        $this->drop_tbl_log = count($found_tables);
    }

    /**
     * Write Log
     *
     * @return void
     */
    protected function writeLog()
    {
        $this->dbConnect();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = PrmMng::getInstance();

        Log::info("ERRORS FOUND:\t{$this->dbquery_errs}");
        Log::info("DROPPED TABLES:\t{$this->drop_tbl_log}");
        Log::info("RENAMED TABLES:\t{$this->rename_tbl_log}");
        Log::info("QUERIES RAN:\t{$this->dbquery_rows}\n");

        $this->dbtable_rows  = 1;
        $this->dbtable_count = 0;

        Log::info("TABLES ROWS IN DATABASE AFTER EXTRACTION\n");
        if (($result = DUPX_DB::mysqli_query($this->dbh, "SHOW TABLES")) != false) {
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                $table_rows          = (string) DUPX_DB::countTableRows($this->dbh, $row[0]);
                $this->dbtable_rows += $table_rows;
                Log::info('TABLE ' . str_pad(Log::v2str($row[0]), 50, '_', STR_PAD_RIGHT) . '[ROWS:' . str_pad($table_rows, 6, " ", STR_PAD_LEFT) . ']');
                $this->dbtable_count++;
            }
            @mysqli_free_result($result);
        }

        if ($this->dbtable_count == 0) {
            $tablePrefix = $paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
            $longMsg     = "You may have to manually run the installer-data.sql to validate data input. " .
                "Also check to make sure your installer file is correct and the table prefix '" . $tablePrefix . " is correct for this particular version of WordPress.";
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg' => 'No table in database',
                'level'    => DUPX_NOTICE_ITEM::NOTICE,
                'longMsg'  => $longMsg,
                'sections' => 'database',
            ));
            Log::info("NOTICE: " . $longMsg . "\n");
        }

        $finalReport                              = $paramsManager->getValue(PrmMng::PARAM_FINAL_REPORT_DATA);
        $finalReport['extraction']['table_count'] = $this->dbtable_count;
        $finalReport['extraction']['table_rows']  = $this->dbtable_rows;
        $finalReport['extraction']['query_errs']  = $this->dbquery_errs;
        $paramsManager->setValue(PrmMng::PARAM_FINAL_REPORT_DATA, $finalReport);

        $paramsManager->save();
        $nManager->saveNotices();
    }

    /**
     * Return result data
     *
     * @return mixed[]
     */
    public function getResultData()
    {
        $result                      = array();
        $result['pass']              = $this->post['pass'];
        $result['continue_chunking'] = $this->post['continue_chunking'];
        $totalSize                   = $this->dbDumpIterator->totalSize();
        $totalOffset                 = $this->dbDumpIterator->totalOffset($this->post['pos']);
        if ($result['continue_chunking'] == 0 && $result['pass']) {
            $result['perc']        = '100%';
            $result['queryOffset'] = 'Bytes processed ' . number_format($totalSize) . ' of ' . number_format($totalSize);
        } else {
            $result['perc']        = round(($totalOffset * 100 / $totalSize), 2) . '%';
            $result['queryOffset'] = 'Bytes processed ' . number_format($totalOffset) . ' of ' . number_format($totalSize);
        }
        $result['is_error']    = $this->post['is_error'];
        $result['error_msg']   = $this->post['error_msg'];
        $result['table_count'] = $this->dbtable_count;
        $result['table_rows']  = $this->dbtable_rows;
        $result['query_errs']  = $this->dbquery_errs;

        return $result;
    }

    /**
     * Skip error notice
     *
     * @param string $err   Error message
     * @param string $query the SQL query
     *
     * @return bool if true will skip front-end notice of error message
     */
    private function skipErrorNotice($err, $query)
    {
        if (preg_match(self::SQL_CREATE_VIEW_PROC_FUNC_PATTERN, $query) && DUPX_U::contains($err, "already exists") && InstState::isAddSiteOnMultisite()) {
            return true;
        }

        return false;
    }

    /**
     * Is firt chunk or not chunking
     *
     * @return bool
     */
    protected function firstOrNotChunking()
    {
        return $this->post['first_chunk'] || !PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_CHUNK);
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        $this->dbClose();
    }
}
