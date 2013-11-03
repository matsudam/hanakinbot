<?php

include_once ("./initialization.php");
include_once ("./twitter.php");
include_once ("./facebook.php");
include_once (LIB_DIR . "database.php");
include_once (LIB_DIR . "twitteroauth/twitteroauth.php");

// DBクラスの生成
$db = new Database(DB_URI, DB_NAME, DB_USER, DB_PASS);

// MySQLへ接続する
$db->connect();

// トランザクション開始
$db->begin();

// つぶやくワードを初期化
$word = NULL;

try{
	/*
	 * つぶやく内容を設定
	 */

	// パラメータ引数があるか？
	if(!isset($argv[1])) {
		// 引数がない場合は処理終了
		exit(0);
	}

	$bot_id = $argv[1];

	switch ( $bot_id ){
	case 1:		// 毎週金曜日18:48起動
		$day = date("j");
		if($day == 13){
			$word = "今日は13日の金曜日♪";
		}

		break;

	case 2:		// 毎月1日00:05起動
		$month = date("n");
		switch($month){
			case 1:
				$word = "よし、来年から本気出す！";
				break;
			case 4:
				$word = "よし、今月から本気出す！（今日はエイプリルフールです）";
				break;
			default:
				$word = "よし、来月から本気出す！";
		}
		break;

	case 3:		// 今日は仏滅（毎日 9:11起動）
		include_once (LIB_DIR . 'qreki.php');
		// 六曜の取得：0:大安 1:赤口 2:先勝 3:友引 4:先負 5:仏滅
		$rokuyo = get_rokuyou(date('Y'), date('n'), date('j'));
		if($rokuyo != 5)	exit(0);
		$word = "今日は仏滅！";
		break;

	case 34:	// 山本昌bot
		break;

	case 80:	// バルス
		$word = "バルス";	
		break;

	case 99:	// テスト投稿。
		//$word = "これはテスト投稿です。";
		$word = "あー、あー、テス、テスッ、これはテスト投稿です。";
	}

	if(!$word){
		// ツイートする内容を取得
		$word_sql = "SELECT word FROM words A where A.delete=0 and category=" . $bot_id . " ORDER BY RAND() LIMIT 1";
		$word_result = $db->query( $word_sql );
		$word_row = $db->fetch( $word_result );
		$word = $word_row['word'];
	}

	/*
	 * tokenの取得
	 */
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


	/*
	 * TwitterにPOST
	 */
	post_twitter( $word, $bot_id, $twitter_consumer_key, $twitter_consumer_secret );

	/*
	 * facebookにPOST
	 */
	post_facebook( $word, $bot_id, $facebook_access_token );


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
