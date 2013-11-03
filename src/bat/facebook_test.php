<?php

	header("Content-Type: application/xml");


	$url = "https://graph.facebook.com/100000744726911/feed"
			. '?access_token=310936485616667|A-lhnZ9KWNy1_t5uuygDrpJv6IM'
			. '&message=もう一度';

	require_once('HTTP/Request2.php');
	$request = new HTTP_Request2($url, HTTP_Request2::METHOD_POST);
	$request->setConfig(array(
			//'proxy_host' => 'proxy.example.net',
			//'proxy_port' => 3128,
			'ssl_verify_peer' => false
	));
	$response = $request->send();
	if ($response->getStatus() / 100 != 2) {
		echo $response->getReasonPhrase() . ' : user_id=' . $row['user_id']  . " (facebook)¥n";
//			die();
	}
?>