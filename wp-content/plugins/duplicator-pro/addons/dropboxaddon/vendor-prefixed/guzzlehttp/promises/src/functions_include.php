<?php

namespace VendorDuplicator\Dropbox;

// Don't redefine the functions if included multiple times.
if (!\function_exists('VendorDuplicator\Dropbox\GuzzleHttp\Promise\promise_for')) {
    require __DIR__ . '/functions.php';
}
