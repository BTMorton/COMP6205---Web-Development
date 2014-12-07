<?php

require_once("signedjsonwebtoken.php");

$key = 'uCSMdetDReY4MAyVTh7fuq7hZ3tYk9Gyc3GZWpCR3LWkSgmHm9bwzWR2pbnTMzUecuR4mAMj';

if (!empty($_POST) && !empty($_POST['segments'])) {
	$signer = new SignedJSONWebToken($key);
	$token = $signer->sign($_POST['segments']);

	echo "http://".$_SERVER['SERVER_NAME'].pathinfo($_SERVER['REQUEST_URI'], PATHINFO_DIRNAME)."/?segments=".$token;
}
die('');