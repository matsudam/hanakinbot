<?php
include_once ('../src/lib/twitteroauth/twitteroauth.php');
function twitterOAuth(){
	global $twitter_consumer_key, $twitter_consumer_secret;

	// OAuthオブジェクト生成
	$to = new TwitterOAuth($twitter_consumer_key, $twitter_consumer_secret);

	// callbackURLを指定してRequest tokenを取得
	$tok = $to->getRequestToken(CALLBACK);

	// セッションに保存
	$_SESSION['twitter_token']=$token=$tok['oauth_token'];
	$_SESSION['twitter_token_secret'] = $tok['oauth_token_secret'];

	// サインインするためのURLを取得
	$url = $to->getAuthorizeURL($token);

	return $url;
}

function old_twitterOAuth( $smarty ){
	global $twitter_consumer_key, $twitter_consumer_secret;

	// セッションにアクセストークンがなかったらloginページに飛ぶ
	if((!isset($_SESSION['oauth_token']) || $_SESSION['oauth_token']===NULL) && (!isset($_SESSION['oauth_token_secret']) || $_SESSION['oauth_token_secret']===NULL)){
		// 未サインイン
		
		// OAuthオブジェクト生成
		$to = new TwitterOAuth($twitter_consumer_key, $twitter_consumer_secret);

		// callbackURLを指定してRequest tokenを取得
		$tok = $to->getRequestToken(CALLBACK);

		// セッションに保存
		$_SESSION['request_token']=$token=$tok['oauth_token'];
		$_SESSION['request_token_secret'] = $tok['oauth_token_secret'];

		// サインインするためのURLを取得
		$url = $to->getAuthorizeURL($token);

		$smarty->assign('signin_url', $url);

		return FALSE;
	}else{
		// サインイン済み
		if(isset($_SESSION['user_id']) && isset($_SESSION['screen_name'])) {
			$smarty->assign('user_id', $_SESSION['user_id']);
			$smarty->assign('screen_name', $_SESSION['screen_name']);
		}
		
		return TRUE;
	}
}
?>