<?php
function post_twitter( $word, $bot_id, $twitter_consumer_key, $twitter_consumer_secret ) 
{
	global $db;

	//$sql = "SELECT oauth_token, oauth_token_secret FROM twitters";
	$sql = "SELECT oauth_token, oauth_token_secret FROM twitters INNER JOIN users ON twitters.user_id=users.user_id INNER JOIN bot_entry ON twitters.user_id=bot_entry.user_id WHERE users.delete=0 and bot_entry.bot_id=" . $bot_id;
	$result = $db->query( $sql );

	header("Content-Type: application/xml");

	while ($row = $db->fetch( $result )){
		// Access Tokenの値
		$access_token = $row['oauth_token'];
		// Access Token Secretの値
		$access_token_secret = $row['oauth_token_secret'];
		
		// OAuthオブジェクト生成
		$to = new TwitterOAuth($twitter_consumer_key, $twitter_consumer_secret, $access_token, $access_token_secret);

		// TwitterへPOSTする。パラメーターは配列に格納する
		// in_reply_to_status_idを指定するのならば array("status"=>"@hogehoge reply","in_reply_to_status_id"=>"0000000000"); とする。
		//$req = $to->OAuthRequest("http://api.twitter.com/1/statuses/update.xml","POST",array("status"=>$word));
		$req = $to->OAuthRequest("https://api.twitter.com/1.1/statuses/update.json","POST",array("status"=>$word));
	}
}
?>