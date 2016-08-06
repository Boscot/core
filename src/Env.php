<?php

namespace Boscot;

use Boscot\User;

class Env
{

    public static function init()
    {
        error_reporting(E_ALL);
        ini_set('log_errors', 1);
        ini_set('display_errors', 1);
        if (!defined('DEBUG')) {
            define('DEBUG', 0);
        }
        $timestamp = date('U')."_".preg_replace("/.* (\d+)$/", "$1", microtime(false));

        $request = substr(preg_replace("#[^a-zA-Z0-9\-_]+#", "_", $_SERVER['REQUEST_URI']), 0, 200);
        $logfile = "${request}_${timestamp}.txt";

        $docroot = $_SERVER['DOCUMENT_ROOT'];
        define('DIALOG', $docroot.'/dialog');
        define('CHEATS', $docroot.'/cheats');

        define('FILE_REQ', DIALOG."/req_$logfile");
        define('FILE_RES', DIALOG."/res_$logfile");
        if (!file_exists(DIALOG)) {
            mkdir(DIALOG);
        }
        if (!file_exists(CHEATS)) {
            mkdir(CHEATS);
        }
        define('IP_FILE', CHEATS.'/ips_'.date('Ymd').'.txt');

        //User::check();


        define("LOCAL_IP", file_get_contents("http://api.ipify.org/"));

    }
}
