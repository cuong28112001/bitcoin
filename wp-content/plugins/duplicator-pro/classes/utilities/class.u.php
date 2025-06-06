<?php

/**
 * Utility class used for various task
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package    DUP_PRO
 * @subpackage classes/utilities
 * @copyright  (c) 2017, Snapcreek LLC
 * @license    https://opensource.org/licenses/GPL-3.0 GNU Public License
 */

defined("ABSPATH") or die("");

use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\Snap\SnapUtil;

class DUP_PRO_U
{
    /**
     * return absolute path for the directories that are core directories
     *
     * @param bool $original If true it returns yes the original realpaths and paths, in case they are links, Otherwise it returns only the realpaths.
     *
     * @return string[]
     */
    public static function getWPCoreDirs($original = false)
    {
        $corePaths   = DUP_PRO_Archive::getArchiveListPaths();
        $corePaths[] = $corePaths['abs'] . '/wp-admin';
        $corePaths[] = $corePaths['abs'] . '/wp-includes';

        if ($original) {
            $origPaths   = DUP_PRO_Archive::getOriginalPaths();
            $origPaths[] = $origPaths['abs'] . '/wp-admin';
            $origPaths[] = $origPaths['abs'] . '/wp-includes';

            $corePaths = array_merge($corePaths, $origPaths);
        }

        return array_values(array_unique($corePaths));
    }

    /**
     * return absolute path for the files that are core directories
     *
     * @return string[]
     */
    public static function getWPCoreFiles()
    {
        return array(DUP_PRO_Archive::getArchiveListPaths('wpconfig') . '/wp-config.php');
    }

    /**
     * Converts an absolute path to a relative path
     *
     * @param string $from The the path relative to $to
     * @param string $to   The full path of the directory to transform
     *
     * @return string  A string of the result
     */
    public static function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to   = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
        $from = str_replace('\\', '/', $from);
        $to   = str_replace('\\', '/', $to);

        $from    = explode('/', $from);
        $to      = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath   = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    //$relPath[0] = './' . $relPath[0];
                }
            }
        }
        return implode('/', $relPath);
    }

    /**
     * Gets the percentage of one value to another
     * example:
     *     $val1 = 100
     *     $val2 = 400
     *     $res  = 25
     *
     * @param int|float $val1      The value to calculate the percentage
     * @param int|float $val2      The total value to calculate the percentage against
     * @param int       $precision The number of decimal places to round to
     *
     * @return float  Returns the results
     */
    public static function percentage($val1, $val2, $precision = 0)
    {
        $division = $val1 / (float) $val2;
        $res      = $division * 100;
        return round($res, $precision);
    }

    /**
     * Display human readable byte sizes
     *
     * @param int $size The size in bytes
     *
     * @return string The size of bytes readable such as 100KB, 20MB, 1GB etc.
     */
    public static function byteSize($size)
    {
        try {
            $size  = (int) $size;
            $units = array(
                'B',
                'KB',
                'MB',
                'GB',
                'TB',
            );
            for ($i = 0; $size >= 1024 && $i < 4; $i++) {
                $size /= 1024;
            }
            return round($size, 2) . $units[$i];
        } catch (Exception $e) {
            return "n/a";
        }
    }

    /**
     * Return a string with the elapsed time in seconds
     *
     * @see getMicrotime()
     *
     * @param int|float $end   The final time in the sequence to measure
     * @param int|float $start The start time in the sequence to measure
     *
     * @return string   The time elapsed from $start to $end as 5.89 sec.
     */
    public static function elapsedTime($end, $start)
    {

        return sprintf(
            esc_html_x(
                '%.3f sec.',
                'sec. stands for seconds',
                'duplicator-pro'
            ),
            abs($end - $start)
        );
    }

    /**
     * Return a float with the elapsed time in seconds
     *
     * @see getMicrotime(), elapsedTime()
     *
     * @param int|float $end   The final time in the sequence to measure
     * @param int|float $start The start time in the sequence to measure
     *
     * @return string   The time elapsed from $start to $end as 5.89
     */
    public static function elapsedTimeU($end, $start)
    {
        return sprintf('%.3f', abs($end - $start));
    }

    /**
     * Gets the contents of the file as an attachment type
     *
     * @param string $filepath    The full path the file to read
     * @param string $contentType The header content type to force when pushing the attachment
     *
     * @return void
     */
    public static function getDownloadAttachment($filepath, $contentType)
    {
        // Clean previous or after eny notice texts
        ob_clean();
        ob_start();
        $filename = basename($filepath);

        header("Content-Type: {$contentType}");
        header("Content-Disposition: attachment; filename={$filename}");
        header("Pragma: public");

        if (readfile($filepath) === false) {
            $msg = sprintf(__('Couldn\'t read %s', 'duplicator-pro'), $filepath);
            throw new Exception($msg);
        }
        ob_end_flush();
    }

    /**
     * Return the path of an executable program
     *
     * @param string $exeFilename A file name or path to a file name of the executable
     *
     * @return string|null Returns the full path of the executable or null if not found
     */
    public static function getExeFilepath($exeFilename)
    {
        $filepath = null;

        if (!Shell::test()) {
            return null;
        }

        $shellOutput = Shell::runCommand("hash $exeFilename 2>&1", Shell::AVAILABLE_COMMANDS);
        if ($shellOutput !== false && $shellOutput->isEmpty()) {
            $filepath = $exeFilename;
        } else {
            $possible_paths = array(
                "/usr/bin/$exeFilename",
                "/opt/local/bin/$exeFilename",
            );

            foreach ($possible_paths as $path) {
                if (@file_exists($path)) {
                    $filepath = $path;
                    break;
                }
            }
        }

        return $filepath;
    }

    /**
     * Get current microtime as a float.  Method is used for simple profiling
     *
     * @see elapsedTime
     *
     * @return float  A float in the form "msec sec", where sec is the number of seconds since the Unix epoch
     */
    public static function getMicrotime()
    {
        return microtime(true);
    }

    /**
     * Verifies that a correct security nonce was used. If correct nonce is not used, It will cause to die
     *
     * A nonce is valid for 24 hours (by default).
     *
     * @param string     $nonce  Nonce value that was used for verification, usually via a form field.
     * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
     *
     * @return void
     */
    public static function verifyNonce($nonce, $action)
    {
        if (!wp_verify_nonce($nonce, $action)) {
            die('Security issue');
        }
    }

    /**
     * Does the current user have the capability
     * Dies if user doesn't have the correct capability
     *
     * @return void
     */
    public static function checkAjax()
    {
        if (!wp_doing_ajax()) {
            $errorMsg = esc_html__('You do not have called from AJAX to access this page.', 'duplicator-pro');
            DUP_PRO_Log::trace($errorMsg);
            SnapUtil::errorLog($errorMsg);
            wp_die(esc_html($errorMsg));
        }
    }

    /**
     * Sets a value or returns a default
     *
     * @param mixed $val     The value to set
     * @param mixed $default The value to default to if the val is not set
     *
     * @return mixed  A value or a default
     */
    public static function setVal($val, $default = null)
    {
        return isset($val) ? $val : $default;
    }

    /**
     * Check is set and not empty, sets a value or returns a default
     *
     * @param mixed $val     The value to set
     * @param mixed $default The value to default to if the val is not set
     *
     * @return mixed  A value or a default
     */
    public static function isEmpty($val, $default = null)
    {
        return isset($val) && !empty($val) ? $val : $default;
    }

    /**
     * Returns the last N lines of a file. Simular to tail command
     *
     * @param string $filepath The full path to the file to be tailed
     * @param int    $lines    The number of lines to return with each tail call
     *
     * @return false|string The last N parts of the file, false on failure
     */
    public static function tailFile($filepath, $lines = 2)
    {
        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) {
            return false;
        }

        // Sets buffer size
        $buffer = 256;

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") {
            $lines -= 1;
        }

        // Start reading
        $output = '';
        $chunk  = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk  = fread($f, $seek)) . $output;
            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        fclose($f);
        return trim($output);
    }

    /**
     * Check given table is exist in real
     *
     * @param string $table string Table name
     *
     * @return bool
     */
    public static function isTableExists($table)
    {
        global $wpdb;
        // It will clear the $GLOBALS['wpdb']->last_error var
        $wpdb->flush();
        $sql = "SELECT 1 FROM `" . esc_sql($table) . "` LIMIT 1;";

        $wpdb->get_var($sql);
        if (empty($wpdb->last_error)) {
            return true;
        }
        return false;
    }

    /**
     * Finds if its a valid executable or not
     *
     * @param string $cmd A non zero length executable path to find if that is executable or not.
     *
     * @return bool
     */
    public static function isExecutable($cmd)
    {
        if (strlen($cmd) == 0) {
            return false;
        }

        if (
            @is_executable($cmd)
            || !Shell::runCommand($cmd, Shell::AVAILABLE_COMMANDS)->isEmpty()
            || !Shell::runCommand($cmd . ' -?', Shell::AVAILABLE_COMMANDS)->isEmpty()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Look into string and try to fix its natural expected value type
     *
     * @param mixed $data Simple string
     *
     * @return mixed value with it's natural string type
     */
    public static function valType($data)
    {
        if (is_string($data)) {
            if (is_numeric($data)) {
                if ((int) $data == $data) {
                    return (int) $data;
                } elseif ((float) $data == $data) {
                    return (float) $data;
                }
            } elseif (in_array(strtolower($data), array('true', 'false'), true)) {
                return ($data == 'true');
            }
        } elseif (is_array($data)) {
            foreach ($data as $key => $str) {
                $data[$key] = DUP_PRO_U::valType($str);
            }
        }
        return $data;
    }

    /**
     * Check given var is curl resource or instance of CurlHandle or CurlMultiHandle
     *  It is used for check curl_init() return, because
     *      curl_init() returns resource in lower PHP version than 8.0
     *      curl_init() returns class instance in PHP version 8.0
     *  Ref. https://php.watch/versions/8.0/resource-CurlHandle
     *
     * @param resource|object $var var to check
     *
     * @return boolean
     */
    public static function isCurlResourceOrInstance($var)
    {
        // CurlHandle class instance return of curl_init() in php 8.0
        // CurlMultiHandle class instance return of curl_multi_init() in php 8.0

        if (is_resource($var) || ($var instanceof CurlHandle) || ($var instanceof CurlMultiHandle)) {
            return true;
        } else {
            return false;
        }
    }
}
