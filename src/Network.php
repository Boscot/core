<?php

namespace Boscot;

use Boscot\Logger;

class Network
{


    /*
    if (!function_exists('gzdecode')) {
     function gzdecode($string) { // no support for 2nd argument
      return file_get_contents('compress.zlib://data:who/cares;base64,'. base64_encode($string));
     }
    }

    #$code = gzdecode(file_get_contents("php://input"));
    Logger::append('truc',file_get_contents("php://input","r"));
    $gh = gzopen("php://input","r");
    $code = gzread($gh,10240);
    gzclose($gh);
    Logger::append('truc', $code);
    Logger::append('truc', "\n================\n");
    Logger::append('truc', gzdecode($code));
    Logger::append('truc', "\n================\n");
    Logger::append('truc', gzuncompress($code));
    Logger::append('truc', "\n================\n");
    Logger::append('truc', implode("\n",gzfile($code)));
    Logger::append('truc', "\n================\n");
    Logger::append('truc', zlib_decode($code));
    exit;
    */


    /*
    public static function decode_chunked($str)
    {
        for ($res = ''; !empty($str); $str = trim($str)) {
            $pos = strpos($str, "\r\n");
            $len = hexdec(substr($str, 0, $pos));
            $res.= substr($str, $pos + 2, $len);
            $str = substr($str, $pos + 2 + $len);
        }
        return $res;
    }
     */

    public static function readHeaders()
    {
        $headers = "";
        $method = isset($_SERVER['REDIRECT_REQUEST_METHOD']) ? $_SERVER['REDIRECT_REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
        #    $method = 'POST';
        $uri = $_SERVER['REQUEST_URI'];
        #    $uri = preg_replace('#gems=#','gems=1',$uri);
        #    $headers .= $method." ".$uri." ".$_SERVER['SERVER_PROTOCOL']."\n";
        $headers .= $method." ".$uri." HTTP/1.0\n";
        foreach (apache_request_headers() as $header => $value) {
            if (preg_match("/deflate/", $value)) {
                continue;
            }
            if (preg_match("/gzip/", $value)) {
                continue;
            }
            if (preg_match("/keep-alive/", $value)) {
                continue;
            }
            #if (preg_match("/Content-Length/",$header)) { continue; }
            $headers .= "$header: $value \n";
        }
        $headers .= "\n";
        $headers .= file_get_contents("php://input");
        $headers .= "\n";
        $headers = preg_replace('/^ $/', '', $headers);
        $headers .= "\n";
        return $headers;
    }

    public static function playHeaders($file)
    {
        Logger::log("playing headers \n");
        $host = $_SERVER['SERVER_NAME'];
        $host_ip = gethostbyname($host);
        if ($host_ip == LOCAL_IP) {
            Logger::log("Warning, loop deteced. Playing headers to ourself. Break.");
            echo "Loop.";
            exit;
        }

        Logger::log("cat '$file' | netcat $host 80");
        exec("/bin/cat '$file' | /bin/netcat $host 80", $output);
        $response = implode("\n", $output);

        Logger::log(print_r($response, 1));
        return $response;
    }

    /*
    public static function play_headers_content($file) {
        $file = __DIR__."/".$file;
     $host = $_SERVER['SERVER_NAME'];
     Logger::log("cat '$file' | netcat $host 80");
     exec("/bin/cat $file | /bin/netcat $host 80",$output);
     $res = implode("\n",$output);
        return $res;
    }
    */

    public static function replyHeaders($file, $show_headers = true)
    {
        $lines = file($file);
        $headers = true;
        foreach ($lines as $line) {
            if ($line == "\n") {
                $headers = false;
                continue;
            }
            if ($headers) {
                #$line = preg_replace("#HTTP/1.0#","HTTP/1.1",$line);
                if ($show_headers) {
                    header($line);
                }
            } else {
                echo $line;
            }
        }
    }


    public static function getRequest()
    {
        Logger::log("reading headers \n");
        $headers = static::readHeaders();
        Logger::log(print_r($headers, 1));
        return $headers;
    }
}
