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
use VendorDuplicator\Symfony\Polyfill\Mbstring as p;
if (!\function_exists('\\VendorDuplicator\\mb_convert_encoding') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_convert_encoding')) {
    function mb_convert_encoding($string, $to_encoding, $from_encoding = null)
    {
        return p\Mbstring::mb_convert_encoding($string, $to_encoding, $from_encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_decode_mimeheader') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_decode_mimeheader')) {
    function mb_decode_mimeheader($string)
    {
        return p\Mbstring::mb_decode_mimeheader($string);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_encode_mimeheader') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_encode_mimeheader')) {
    function mb_encode_mimeheader($string, $charset = null, $transfer_encoding = null, $newline = null, $indent = null)
    {
        return p\Mbstring::mb_encode_mimeheader($string, $charset, $transfer_encoding, $newline, $indent);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_decode_numericentity') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_decode_numericentity')) {
    function mb_decode_numericentity($string, $map, $encoding = null)
    {
        return p\Mbstring::mb_decode_numericentity($string, $map, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_encode_numericentity') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_encode_numericentity')) {
    function mb_encode_numericentity($string, $map, $encoding = null, $hex = \false)
    {
        return p\Mbstring::mb_encode_numericentity($string, $map, $encoding, $hex);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_convert_case') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_convert_case')) {
    function mb_convert_case($string, $mode, $encoding = null)
    {
        return p\Mbstring::mb_convert_case($string, $mode, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_internal_encoding') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_internal_encoding')) {
    function mb_internal_encoding($encoding = null)
    {
        return p\Mbstring::mb_internal_encoding($encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_language') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_language')) {
    function mb_language($language = null)
    {
        return p\Mbstring::mb_language($language);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_list_encodings') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_list_encodings')) {
    function mb_list_encodings()
    {
        return p\Mbstring::mb_list_encodings();
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_encoding_aliases') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_encoding_aliases')) {
    function mb_encoding_aliases($encoding)
    {
        return p\Mbstring::mb_encoding_aliases($encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_check_encoding') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_check_encoding')) {
    function mb_check_encoding($value = null, $encoding = null)
    {
        return p\Mbstring::mb_check_encoding($value, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_detect_encoding') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_detect_encoding')) {
    function mb_detect_encoding($string, $encodings = null, $strict = \false)
    {
        return p\Mbstring::mb_detect_encoding($string, $encodings, $strict);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_detect_order') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_detect_order')) {
    function mb_detect_order($encoding = null)
    {
        return p\Mbstring::mb_detect_order($encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_parse_str') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_parse_str')) {
    function mb_parse_str($string, &$result = array())
    {
        \parse_str($string, $result);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strlen') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strlen')) {
    function mb_strlen($string, $encoding = null)
    {
        return p\Mbstring::mb_strlen($string, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strpos') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strpos')) {
    function mb_strpos($haystack, $needle, $offset = 0, $encoding = null)
    {
        return p\Mbstring::mb_strpos($haystack, $needle, $offset, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strtolower') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strtolower')) {
    function mb_strtolower($string, $encoding = null)
    {
        return p\Mbstring::mb_strtolower($string, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strtoupper') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strtoupper')) {
    function mb_strtoupper($string, $encoding = null)
    {
        return p\Mbstring::mb_strtoupper($string, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_substitute_character') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_substitute_character')) {
    function mb_substitute_character($substitute_character = null)
    {
        return p\Mbstring::mb_substitute_character($substitute_character);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_substr') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_substr')) {
    function mb_substr($string, $start, $length = 2147483647, $encoding = null)
    {
        return p\Mbstring::mb_substr($string, $start, $length, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_stripos') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_stripos')) {
    function mb_stripos($haystack, $needle, $offset = 0, $encoding = null)
    {
        return p\Mbstring::mb_stripos($haystack, $needle, $offset, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_stristr') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_stristr')) {
    function mb_stristr($haystack, $needle, $before_needle = \false, $encoding = null)
    {
        return p\Mbstring::mb_stristr($haystack, $needle, $before_needle, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strrchr') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strrchr')) {
    function mb_strrchr($haystack, $needle, $before_needle = \false, $encoding = null)
    {
        return p\Mbstring::mb_strrchr($haystack, $needle, $before_needle, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strrichr') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strrichr')) {
    function mb_strrichr($haystack, $needle, $before_needle = \false, $encoding = null)
    {
        return p\Mbstring::mb_strrichr($haystack, $needle, $before_needle, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strripos') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strripos')) {
    function mb_strripos($haystack, $needle, $offset = 0, $encoding = null)
    {
        return p\Mbstring::mb_strripos($haystack, $needle, $offset, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strrpos') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strrpos')) {
    function mb_strrpos($haystack, $needle, $offset = 0, $encoding = null)
    {
        return p\Mbstring::mb_strrpos($haystack, $needle, $offset, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strstr') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strstr')) {
    function mb_strstr($haystack, $needle, $before_needle = \false, $encoding = null)
    {
        return p\Mbstring::mb_strstr($haystack, $needle, $before_needle, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_get_info') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_get_info')) {
    function mb_get_info($type = 'all')
    {
        return p\Mbstring::mb_get_info($type);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_http_output') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_http_output')) {
    function mb_http_output($encoding = null)
    {
        return p\Mbstring::mb_http_output($encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_strwidth') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_strwidth')) {
    function mb_strwidth($string, $encoding = null)
    {
        return p\Mbstring::mb_strwidth($string, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_substr_count') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_substr_count')) {
    function mb_substr_count($haystack, $needle, $encoding = null)
    {
        return p\Mbstring::mb_substr_count($haystack, $needle, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_output_handler') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_output_handler')) {
    function mb_output_handler($string, $status)
    {
        return p\Mbstring::mb_output_handler($string, $status);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_http_input') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_http_input')) {
    function mb_http_input($type = '')
    {
        return p\Mbstring::mb_http_input($type);
    }
}
if (\PHP_VERSION_ID >= 80000) {
    require_once __DIR__ . '/Resources/mb_convert_variables.php8';
} elseif (!\function_exists('\\VendorDuplicator\\mb_convert_variables')) {
    function mb_convert_variables($toEncoding, $fromEncoding, &$a = null, &$b = null, &$c = null, &$d = null, &$e = null, &$f = null)
    {
        return p\Mbstring::mb_convert_variables($toEncoding, $fromEncoding, $a, $b, $c, $d, $e, $f);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_ord') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_ord')) {
    function mb_ord($string, $encoding = null)
    {
        return p\Mbstring::mb_ord($string, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_chr') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_chr')) {
    function mb_chr($codepoint, $encoding = null)
    {
        return p\Mbstring::mb_chr($codepoint, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_scrub') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_scrub')) {
    function mb_scrub($string, $encoding = null)
    {
        $encoding = null === $encoding ? \mb_internal_encoding() : $encoding;
        return \mb_convert_encoding($string, $encoding, $encoding);
    }
}
if (!\function_exists('\\VendorDuplicator\\mb_str_split') && !\function_exists('\\VendorDuplicator\\VendorDuplicator\mb_str_split')) {
    function mb_str_split($string, $length = 1, $encoding = null)
    {
        return p\Mbstring::mb_str_split($string, $length, $encoding);
    }
}
if (\extension_loaded('mbstring')) {
    return;
}
if (!\defined('MB_CASE_UPPER')) {
    \define('MB_CASE_UPPER', 0);
}
if (!\defined('MB_CASE_LOWER')) {
    \define('MB_CASE_LOWER', 1);
}
if (!\defined('MB_CASE_TITLE')) {
    \define('MB_CASE_TITLE', 2);
}
