<?php

if (!defined('OPENSRS_TIME_LOG')) {
    define('OPENSRS_TIME_LOG', __DIR__ . '/time.log');
}

/**
 * Class OpensrsLogger
 *
 * Log openSRS requests to (hopefully) catch the slow ones
 */
class OpensrsLogger {

    protected static $startTime;
    protected static $callArray;

    public static function start($callArray) {
        static::$startTime = microtime(true);
        static::$callArray = $callArray;
    }

    public static function end() {
        $timeNow = new DateTime();
        $duration = microtime(true) - static::$startTime;
        $entry = array(
            $timeNow->format('Y-m-d H:i:s'),
            $duration,
            print_r(static::$callArray, true)
        );
        $handle = @fopen(OPENSRS_TIME_LOG, 'a');
        if ($handle !== false) {
            fputcsv($handle, $entry);
            fclose($handle);
        }
    }
}