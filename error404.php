<? 

include('lib.php');
define(DEBUG,1);

log_request();

logger("playing headers \n");
$response = play_headers($file_req);
logger(print_r($response,1));
append($file_res,$response);

reply_headers($file_res);


