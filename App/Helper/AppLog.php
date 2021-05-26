<?php
/**
 * @author Hi Developer
 * @date   2021-05-26
 */

namespace App\Helper;


class AppLog
{
    public static function debug($msg, $category = 'debug')
    {
        echo $msg . "\n";
    }

    public static function warning($msg, $category = 'warning')
    {
        echo $msg . "\n";
    }

    public static function error($msg, $category = 'error')
    {
        echo $msg . "\n";
    }
}
