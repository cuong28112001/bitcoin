<?php

namespace VendorDuplicator\Dropbox;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use VendorDuplicator\Dropbox\Symfony\Polyfill\Intl\Normalizer as p;
if (!\function_exists('normalizer_is_normalized') && !\function_exists('VendorDuplicator\Dropbox\normalizer_is_normalized')) {
    function normalizer_is_normalized($string, $form = p\Normalizer::FORM_C)
    {
        return p\Normalizer::isNormalized((string) $string, (int) $form);
    }
}
if (!\function_exists('normalizer_normalize') && !\function_exists('VendorDuplicator\Dropbox\normalizer_normalize')) {
    function normalizer_normalize($string, $form = p\Normalizer::FORM_C): string|false
    {
        return p\Normalizer::normalize((string) $string, (int) $form);
    }
}
