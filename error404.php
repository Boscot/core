<?php

$autoload = $_SERVER['DOCUMENT_ROOT']."/vendor/autoload.php";;
require_once $autoload;

use Boscot\Env;
use Boscot\Logger;
use Boscot\Network;
use Boscot\User;

define(DEBUG, 1);

Env::init();

$request = Network::getRequest();
Logger::append(FILE_REQ, $request);

$response = Network::playHeaders(FILE_REQ);

Logger::append(FILE_RES, $response);

Network::replyHeaders(FILE_RES);
/*
ob_start();
Network::replyHeaders(FILE_RES);
$body = ob_get_clean();
print_r($body);
*/


