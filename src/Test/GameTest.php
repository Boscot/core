<?php

namespace Boscot\Test;

/**
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPL v3
 * @link     None
 */
class GameTest extends \PHPUnit_Framework_TestCase
{
    const DOMAIN = "localhost";
    const PORT   = 80;

    public function __construct()
    {
        if (!defined('DEBUG')) {
            define('DEBUG', false);
        }    

    }

    public $domain = "";

    public function sendData($content, $host, $port = 80)
    {
        if (DEBUG) {
            echo $content."\n";
        }
        $cmd = "echo -n \"$content\" | /bin/netcat $host 80";
        //$i++;
        if (DEBUG) {
            echo $cmd."\n";
        }
        exec($cmd, $output);
        if (DEBUG) {
            print_r($output);
        }
        return implode("\n", $output);
    }
}
