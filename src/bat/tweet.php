<?php

include_once ("./initialization.php");
include_once (LIB_DIR . "database.php");
include_once (LIB_DIR . "twitteroauth/twitteroauth.php");

// DBクラスの生成
$db = new Database(DB_URI, DB_NAME, DB_USER, DB_PASS);

// MySQLへ接続する
$db->connect();

// トランザクション開始
$db->begin();


// Consumer keyの値
$consumer_key = TWITTER_CONSUMER_KEY;
// Consumer secretの値
$consumer_secret = TWITTER_CONSUMER_SECRET;

try{ 

	$sql = "SELECT oauth_token, oauth_token_secret FROM twitters A where A.delete=0";
	$result = $db->query( $sql );

	header("Content-Type: application/xml");

	while ($row = $db->fetch( $result )){
		// Access Tokenの値
		$access_token = $row['oauth_token'];
		// Access Token Secretの値
		$access_token_secret = $row['oauth_token_secret'];
		
		// OAuthオブジェクト生成
		$to = new TwitterOAuth($consumer_key,$consumer_secret,$access_token,$access_token_secret);

		// ツイートする内容を取得
		$word_sql = "SELECT word FROM words A where A.delete=0 ORDER BY RAND() LIMIT 1";
		$word_result = $db->query( $word_sql );
		$word_row = $db->fetch( $word_result );
		$word = $word_row['word'];
//		$word = "今日は13日の金曜日♪";
		
		// TwitterへPOSTする。パラメーターは配列に格納する
		// in_reply_to_status_idを指定するのならば array("status"=>"@hogehoge reply","in_reply_to_status_id"=>"0000000000"); とする。
		$req = $to->OAuthRequest("http://api.twitter.com/1/statuses/update.xml","POST",array("status"=>$word));
		
		echo $req;
		echo $word;
	}
	
} catch (DbException $e){
	$db->rollback();
	echo "DBエラー発生:" . $e->getMessage();
	
} catch (Exception $e){ 
	echo "エラー発生:" . $e->getMessage();
	exit();
}

// MySQLへの接続を閉じる
$db->close();

?>
