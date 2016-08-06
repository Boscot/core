<?php

namespace Boscot;

class Logger
{

    public static function log($text)
    {
        if (DEBUG) {
            if (is_array($text)) {
                foreach (explode("\n", print_r($text, true)) as $a) {
                    error_log($a);
                }
            } else {
                foreach (explode("\n", print_r($text, true)) as $a) {
                    error_log($a);
                }
                #error_log($text);
            }
        }
    }

    public static function append($file, $content)
    {
        static::log("writing $file\n");
        $fh = fopen($file, "a+");
        static::log($content);
        fwrite($fh, $content);
        fclose($fh);
    }
}
