<? 
#{"status":0,"gifts":[{"gems":1000}],"donated":[{"gems":1000}],"accepted":[{"gems":1000}],"thanks":[{"gems":1000}]}
#exit;
error_reporting(E_ALL);
ini_set('log_errors',1);
ini_set('display_errors',1);
$date = date('U');
$time = preg_replace("/ .*$/","",microtime(0));
$timestamp = "${date}_$time";

$request = preg_replace("#[^a-zA-Z0-9\-_]+#","_",$_SERVER['REQUEST_URI']);
$request = substr($request,0,200);
$docroot = $_SERVER['DOCUMENT_ROOT'];
$file_req = $docroot."/dialog/raw_${request}_${timestamp}.txt";
$file_res = $docroot."/dialog/res_${request}_${timestamp}.txt";
define('IP_FILE', __DIR__.'/ips_'.date('Ymd').'.txt');


/*
if (!function_exists('gzdecode')) {
 function gzdecode($string) { // no support for 2nd argument
  return file_get_contents('compress.zlib://data:who/cares;base64,'. base64_encode($string));
 }
}

#$code = gzdecode(file_get_contents("php://input"));
append('truc',file_get_contents("php://input","r"));
$gh = gzopen("php://input","r");
$code = gzread($gh,10240);
gzclose($gh);
append('truc', $code);
append('truc', "\n================\n");
append('truc', gzdecode($code));
append('truc', "\n================\n");
append('truc', gzuncompress($code));
append('truc', "\n================\n");
append('truc', implode("\n",gzfile($code)));
append('truc', "\n================\n");
append('truc', zlib_decode($code));
exit;
*/

function logger($text) {
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

function append($file,$content) {
 logger("writing $file\n");
 $fh = fopen($file,"a+");
 logger($content);
	fwrite($fh,$content);
	fclose($fh);
}

function decode_chunked($str) {
	for ($res = ''; !empty($str); $str = trim($str)) {
  $pos = strpos($str, "\r\n");
  $len = hexdec(substr($str, 0, $pos));
  $res.= substr($str, $pos + 2, $len);
  $str = substr($str, $pos + 2 + $len);
 }
 return $res;
}

function read_headers() {
	$headers = "";
	$method = isset($_SERVER['REDIRECT_REQUEST_METHOD']) ? $_SERVER['REDIRECT_REQUEST_METHOD'] : $_SERVER['REQUEST_METHOD'];
#	$method = 'POST';
	$uri = $_SERVER['REQUEST_URI'];
#	$uri = preg_replace('#gems=#','gems=1',$uri);
#	$headers .= $method." ".$uri." ".$_SERVER['SERVER_PROTOCOL']."\n";
	$headers .= $method." ".$uri." HTTP/1.0\n";
	foreach (apache_request_headers() as $header => $value) {
		if (preg_match("/deflate/",$value)) { continue; }
		if (preg_match("/gzip/",$value)) { continue; }
		if (preg_match("/keep-alive/",$value)) { continue; }
#		if (preg_match("/Content-Length/",$header)) { continue; }
		$headers .= "$header: $value \n";
	}
	$headers .= "\n";
	#$headers .= gzdecode(file_get_contents("php://input"));
	$headers .= file_get_contents("php://input");
#	$headers = preg_replace('#"activequests": "#', '"activequests": "download_hd,', $headers);
	$headers .= "\n";
	$headers = preg_replace('/^ $/','',$headers);
	$headers .= "\n";
# $headers = preg_replace('#"gems": (\d+),#', '"gems": 1000000,', $headers);
	return $headers;
}

function play_headers($file) {
#	$file = __DIR__."/".$file;
 $host = $_SERVER['SERVER_NAME'];
 logger("cat '$file' | netcat $host 80");
 exec("/bin/cat '$file' | /bin/netcat $host 80",$output);
# print_r($output);
 $res = implode("\n",$output);
	return $res;
}

#function play_headers_content($file) {
#	$file = __DIR__."/".$file;
# $host = $_SERVER['SERVER_NAME'];
# logger("cat '$file' | netcat $host 80");
# exec("/bin/cat $file | /bin/netcat $host 80",$output);
## print_r($output);
# $res = implode("\n",$output);
#	return $res;
#}

function reply_headers($file, $show_headers=true) {
 $lines = file($file);
	$headers = true;
 foreach($lines as $line) {
	 if ($line == "\n") { $headers = false; continue; }
  if ($headers) { 
        		#$line = preg_replace("#HTTP/1.0#","HTTP/1.1",$line); 
			if ($show_headers) {
        			header($line); 
        		}
		 }
		else { echo $line; }
	}
}


function log_request() {
 global $file_req;
	logger("reading headers \n");
	$headers = read_headers();
	logger(print_r($headers,1));
	append($file_req,$headers);
}


/**
	* Gestion des ips 
	*/

function get_ip() {
	return $_SERVER['REMOTE_ADDR'];
}

function check_user() {
    $ip = get_ip();
				if (user_exists($ip)) {
							 logger("User $ip already cheated. exiting");
        exit;
    } else {
								logger("Ok for user $ip to cheat.");
        save_user($ip);
    }
}

function save_user($user_ip) {
	$fh = fopen(IP_FILE, 'a+');
	$fwrite($fh, $user_ip."\n");
	fclose($fh);
}
/*
function clean_users() {
	file_put_contents(IP_FILE, "");
}
*/
function user_exists($user_ip) {
	$ips = file(IP_FILE);
	foreach ($ips as $ip) {
    	if ($user_ip == trim($ip)) {
            return true;
    	}
	}
	return false;
}



