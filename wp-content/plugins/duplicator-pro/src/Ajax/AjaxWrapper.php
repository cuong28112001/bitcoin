<?php

/**
 * @package   Duplicator
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace Duplicator\Ajax;

use DUP_PRO_Handler;
use DUP_PRO_Log;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;
use Exception;

class AjaxWrapper
{
    /**
     * This function wrap a callback and return always a json well formatted output.
     *
     * check nonce and capability if passed and return a json with this format
     * [
     *      success : bool
     *      data : [
     *          funcData : mixed    // callback return data
     *          message : string    // a message for jvascript func (for example an exception message)
     *          output : string     // all normal output wrapped between ob_start and ob_get_clean
     *                              // if $errorUnespectedOutput is true and output isn't empty the json return an error
     *      ]
     * ]
     *
     * @param callable        $callback              callback function
     * @param string          $nonceaction           nonce action
     * @param string          $nonce                 nonce string
     * @param string|string[] $capabilities          if capability is an empty array don't verify capability
     * @param bool            $errorUnespectedOutput if true thorw exception with unespected optput
     *
     * @return never
     */
    public static function json(
        $callback,
        $nonceaction,
        $nonce,
        $capabilities = [],
        $errorUnespectedOutput = true
    ) {
        $error = false;

        $result = array(
            'funcData' => null,
            'output'   => '',
            'message'  => '',
        );

        ob_start();
        try {
            DUP_PRO_Handler::init_error_handler();
            $nonce = SnapUtil::sanitizeNSCharsNewline($nonce);
            if (!wp_verify_nonce($nonce, $nonceaction)) {
                DUP_PRO_Log::trace('Security issue');
                throw new Exception('Security issue');
            }

            if ($capabilities !== []) {
                if (is_scalar($capabilities)) {
                    $capabilities = [$capabilities];
                }

                foreach ($capabilities as $cap) {
                    CapMng::can($cap);
                }
            }

            // execute ajax function
            $result['funcData'] = call_user_func($callback);
        } catch (Exception $e) {
            $error             = true;
            $result['message'] = $e->getMessage();
        }

        $result['output'] = ob_get_clean();
        if ($errorUnespectedOutput && !empty($result['output'])) {
            $error = true;
        }

        if ($error) {
            wp_send_json_error($result);
        } else {
            wp_send_json_success($result);
        }
    }

    /**
     * This function wrap a callback and start a chunked file download.
     * The callback must return a file path.
     *
     * @param callable():false|array{path:string,name:string} $callback              Callback function that return a file path for download or false on error
     * @param string                                          $nonceaction           nonce action
     * @param string                                          $nonce                 nonce string
     * @param string|string[]                                 $capabilities          if capability is an empty string don't verify capability
     * @param bool                                            $errorUnespectedOutput if true thorw exception with unespected optput
     *
     * @return never
     */
    public static function fileDownload(
        $callback,
        $nonceaction,
        $nonce,
        $capabilities = [],
        $errorUnespectedOutput = true
    ) {
        ob_start();
        try {
            DUP_PRO_Handler::init_error_handler();
            $nonce = SnapUtil::sanitizeNSCharsNewline($nonce);
            if (!wp_verify_nonce($nonce, $nonceaction)) {
                DUP_PRO_Log::trace('Security issue');
                throw new Exception('Security issue');
            }

            if ($capabilities !== []) {
                if (is_scalar($capabilities)) {
                    $capabilities = [$capabilities];
                }

                foreach ($capabilities as $cap) {
                    CapMng::can($cap);
                }
            }

            // execute ajax function
            if (($fileInfo = call_user_func($callback)) === false) {
                throw new Exception('Error generating file');
            }

            if (!@file_exists($fileInfo['path'])) {
                throw new Exception('File ' . $fileInfo['path'] . ' not found');
            }

            $result['output'] = ob_get_clean();
            if ($errorUnespectedOutput && !empty($result['output'])) {
                throw new Exception('Unexpected output');
            }

            \Duplicator\Libs\Snap\SnapIO::serveFileForDownload(
                $fileInfo['path'],
                $fileInfo['name'],
                DUPLICATOR_PRO_BUFFER_DOWNLOAD_SIZE
            );
        } catch (Exception $e) {
            DUP_PRO_Log::trace($e->getMessage());
            SnapIO::serverError500();
        }
    }
}
