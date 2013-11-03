<?php
function post_facebook( $word, $bot_id, $facebook_access_token )
{
	global $db;

	//$sql = "SELECT user_id,access_token FROM facebooks";
	$sql = "SELECT facebooks.user_id AS user_id, facebooks.facebook_id AS facebook_id, facebooks.access_token AS access_token FROM facebooks INNER JOIN users ON facebooks.user_id=users.user_id INNER JOIN bot_entry ON facebooks.user_id=bot_entry.user_id WHERE users.delete=0 and bot_entry.bot_id=" . $bot_id;
	$result = $db->query( $sql );

	header("Content-Type: application/xml");

	while ($row = $db->fetch( $result )){

		//$url = "https://graph.facebook.com/me/feed"
		//		. '?access_token=' . $row['access_token']
		//		. '&message=' . urlencode( $word );
		$url = "https://graph.facebook.com/" . $row['facebook_id'] . "/feed"
				. '?access_token=' . $facebook_access_token
				. '&message=' . urlencode( $word );

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
	}
}
?>