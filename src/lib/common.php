<?php
/**
 * ログインチェック
 */
function get_user_id(){
	if( isset($_SESSION['user_id']) && 
		$_SESSION['user_id'] != 0 ){

		// ログイン済み
		return $_SESSION['user_id'];
	} else {
		// 未ログイン
		$_SESSION = array();
		return 0;
	}
}

/**
 * リダイレクト
 */
function redirect($url){
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " . $url);
}

/**
 * メッセージ取得
 */
function get_message($err, $language){
	global $db, $message1, $message2;
	if($err==0){
		$message1 = $message2 = NULL;
	}else{
		$sql = "select message1, message2 from messages A where msg_id=" . $err . " and language=" . $language;
		$result = $db->query($sql);
		$row = $db->fetch( $result );
		$message1 = $row['message1'];
		$message2 = $row['message2'];
	}
}

/**
 * tokenの取得
 */
function getToken(){
	global $db;
	global $twitter_consumer_key, $twitter_consumer_secret;
	global $facebook_app_id, $facebook_app_secret, $facebook_access_token;

	$token_sql = "SELECT id, token FROM token A ORDER BY id";
	$token_result = $db->query( $token_sql );
	while ($row = $db->fetch( $token_result )){
		switch( $row['id'] ){
		case TWITTER_CONSUMER_KEY:
			$twitter_consumer_key = $row['token'];
			break;
		case TWITTER_CONSUMER_SECRET:
			$twitter_consumer_secret = $row['token'];
			break;
		case FACEBOOK_APP_ID:
			$facebook_app_id = $row['token'];
			break;
		case FACEBOOK_APP_SECRET:
			$facebook_app_secret = $row['token'];
			break;
		case FACEBOOK_ACCESS_TOKEN:
			$facebook_access_token = $row['token'];
			break;
		}
	}
}

/**
 * ランダムな文字列を生成する。
 * @param int $nLengthRequired 必要な文字列長。省略すると 8 文字
 * @return String ランダムな文字列
 */
function getRandomString($nLengthRequired = 8){
	$sCharList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
	mt_srand();
	$sRes = "";
	for($i = 0; $i < $nLengthRequired; $i++)
		$sRes .= $sCharList{mt_rand(0, strlen($sCharList) - 1)};
	return $sRes;
}
?>