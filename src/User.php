<?php

namespace Boscot;

use Boscot\Logger;

class User
{
    /**
     * Gestion des ips
     */

    public static function getIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function isWhitelisted($ip)
    {

        $whites = array(
            '192.168.0.254',  # INTRA
            '127.0.0.1',      # LOCALHOST
        );
        foreach ($whites as $white) {
            if ($ip == $white) {
                return true;
            }
        }
        return false;

    }

    public static function check()
    {
        $ip = static::getIP();
        if (User::isWhitelisted($ip)) {
            Logger::log("Ok user $ip is whitelisted.");
            static::save($ip);
        } elseif (static::exists($ip)) {
            Logger::log("User $ip already cheated. exiting");
            exit;
        } else {
            Logger::log("Ok for user $ip to cheat.");
            static::save($ip);
        }
    }

    public static function save($user_ip)
    {
        Logger::log(IP_FILE);
        $fh = fopen(IP_FILE, 'a+');
        fwrite($fh, $user_ip."\n");
        fclose($fh);
    }
    /*
    function clean_users() {
        file_put_contents(IP_FILE, "");
    }
    */
    public static function exists($user_ip)
    {
        if (!file_exists(IP_FILE)) {
            return false;
        }
        $ips = file(IP_FILE);
        foreach ($ips as $ip) {
            if ($user_ip == trim($ip)) {
                return true;
            }
        }
        return false;
    }
}
