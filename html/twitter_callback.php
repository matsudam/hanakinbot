<?php
	try{ 

		include_once ('../src/initialization.php');
		include_once (LIB_DIR . 'database.php');
		include_once (LIB_DIR . 'common.php');
		
		session_start();
		
		require_once('../src/lib/twitteroauth/twitteroauth.php');

		// DBクラスの生成
		$db = new Database(DB_URI, DB_NAME, DB_USER, DB_PASS);
		// MySQLへ接続する
		$db->connect();

		// トランザクション開始
		$db->begin();


		// パラメータに「oauth_token」か「oauth_verifier」のどちらかがなければ承認拒否とみなして処理終了
		if( !isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier']) ){
			// リダイレクト
			redirect(HOME_URL);
			exit;
		}

		// パラメータからoauth_tokenを取得
		$token = $_GET['oauth_token'];
		// パラメータからoauth_verifierを取得
		$verifier = $_GET['oauth_verifier'];

		// Token情報の取得
		getToken();

		// OAuthオブジェクト生成
		$to = new TwitterOAuth(	$twitter_consumer_key,
								$twitter_consumer_secret,
								$_SESSION['twitter_token'],
								$_SESSION['twitter_token_secret']);

		// oauth_verifierを使ってAccess tokenを取得
		$access_token = $to->getAccessToken($verifier);

		// token keyとtoken secret, user_id, screen_nameをセッションに保存
		$_SESSION['twitter_token'] = $access_token['oauth_token'];	
		$_SESSION['twitter_token_secret'] = $access_token['oauth_token_secret'];
		
		//TwitterのID(数値です)
		$twitter_id  = $access_token['user_id'];
		$_SESSION['twitter_id'] = $twitter_id;

		//スクリーンネーム(いわゆる、アドレスバーに表示される部分です)
		$_SESSION['twitter_screen_name'] = $access_token['screen_name'];
		
		//この後に、初めてのログインであればIDをMySQLに登録し、ユーザー情報登録ページに飛んでいます

		// Twitterの情報を取得
		//$twitter_api_url = "http://api.twitter.com/1/users/show.json?user_id=" . $_SESSION['twitter_id'];
		$req = $to->OAuthRequest("http://api.twitter.com/1.1/users/show.json","GET",array("user_id"=>$twitter_id));

		//$twitter_user = json_decode(file_get_contents($twitter_api_url));
		$twitter_user = json_decode($req);
		$smarty->assign('user_name', $twitter_user->name);
		$smarty->assign('profile_img', $twitter_user->profile_image_url);
		



		
		// twiter_id から user_id を取得
		$sql = "select user_id from twitters A where A.twitter_id=" . $twitter_id;
		$result = $db->query($sql);
		$count = $db->count($result);

		if($count == 0){
			// 新規登録
			// usersテーブルに追加
			$sql = "INSERT INTO users (name,image_url,insert_date,update_date) ";

			$values = "VALUES(" . "'" . $twitter_user->name . "',"
								. "'" . $twitter_user->profile_image_url . "',"
								. "sysdate(),"
								. "sysdate()"
							. ");";

			$sql = $sql . $values;
			$result = $db->query( $sql );

			// twittersテーブルに追加
			$user_id = mysql_insert_id();
			$sql = "INSERT INTO twitters (twitter_id,user_id,oauth_token,oauth_token_secret,screen_name,insert_date,update_date) ";

			$values = "VALUES(" . $twitter_id . ","
								. $user_id . ","
								. "'" . $_SESSION['twitter_token'] . "',"
								. "'" . $_SESSION['twitter_token_secret'] . "',"
								. "'" . $_SESSION['twitter_screen_name'] . "',"
								. "sysdate(),"
								. "sysdate()"
							. ");";
			$sql = $sql . $values;
			$result = $db->query($sql);

			// bot_entryテーブルに追加
			$sql = "INSERT INTO bot_entry (user_id,bot_id,insert_date) ";
			$values = "VALUES(" . $user_id . ","
								. "1,"
								. "sysdate()"
							. ");";
			$sql = $sql . $values;
			$result = $db->query($sql);

			// エラーチェック
			// **add code

		} else if($count == 1) {
			// 情報更新
			$row	= $db->fetch( $result );
			$user_id= $row['user_id'];

			// usersテーブルを更新
			$sql = "UPDATE users A set "
								. "name='" . $twitter_user->name . "',"
								. "image_url='" . $twitter_user->profile_image_url . "',"
								. "update_date=sysdate() "
								. "where A.user_id=" . $user_id . " and A.delete=0";

			$result = $db->query($sql);

			// twittersテーブルを更新
			$sql = "UPDATE twitters A set "
								. "oauth_token='" . $_SESSION['twitter_token'] . "',"
								. "oauth_token_secret='" . $_SESSION['twitter_token_secret'] . "',"
								. "screen_name='" . $_SESSION['twitter_screen_name'] . "',"
								. "update_date=sysdate() "
								. "where A.twitter_id=" . $twitter_id;

			$result = $db->query($sql);

			// エラーチェック
			// **add code

		} else {
			// 有効なtwitter_idが複数存在した場合はシステムエラー
			// **add code
		}

		// セッション情報として user_id と session_token を $_SESSION と $_COOKIE に保存
		$_SESSION['user_id'] = $user_id;
		$_SESSION['session_token'] = $_SESSION['twitter_token'];

		// リダイレクト
		redirect(HOME_URL);
		
		// コミット
		$db->commit();

	} catch (DbException $e){
		$db->rollback();
		echo "DBエラー発生:" . $e->getMessage();
		
	} catch (Exception $e){ 
		$db->rollback();
		echo "エラー発生:" . $e->getMessage();
		exit();
	}

	// MySQLへの接続を閉じる
	$db->close();

?>
