<?php

namespace VendorDuplicator;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use VendorDuplicator\Symfony\Polyfill\Php70 as p;
if (\PHP_VERSION_ID >= 70000) {
    return;
}
if (!\defined('PHP_INT_MIN')) {
    \define('PHP_INT_MIN', ~\PHP_INT_MAX);
}
if (!\function_exists('\\VendorDuplicator\\intdiv') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\intdiv')) {
    function intdiv($num1, $num2)
    {
        return p\Php70::intdiv($num1, $num2);
    }
}
if (!\function_exists('\\VendorDuplicator\\preg_replace_callback_array') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\preg_replace_callback_array')) {
    function preg_replace_callback_array(array $pattern, $subject, $limit = -1, &$count = 0, $flags = null)
    {
        return p\Php70::preg_replace_callback_array($pattern, $subject, $limit, $count);
    }
}
if (!\function_exists('\\VendorDuplicator\\error_clear_last') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\error_clear_last')) {
    function error_clear_last()
    {
        return p\Php70::error_clear_last();
    }
}
