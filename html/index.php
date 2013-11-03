<?php
/**
 * 「hanakin」サイト
 */

	try{ 
		// session start
		session_start();
 
		include_once ('../src/initialization.php');
		include_once (PAGE_DIR . 'twitter_oauth.php');
		include_once (LIB_DIR . 'common.php');
		include_once (LIB_DIR . 'database.php');
		include_once (LIB_DIR . 'user.php');
		
		// DBクラスの生成
		$db = new Database(DB_URI, DB_NAME, DB_USER, DB_PASS);
		// MySQLへ接続する
		$db->connect();

		// トランザクション開始
		$db->begin();

		// check sign in
		$user = NULL;
		$user_id = get_user_id();

		// Token情報の取得
		getToken();
		
		if($user_id == 0){
			// twitter url
			$url = twitterOAuth();
			$smarty->assign('twitter_url', $url);

			// facebook url
			$_SESSION['application_id'] = $facebook_app_id;
			$_SESSION['application_secret'] = $facebook_app_secret;
			$_SESSION['redirect_uri'] = HOME_URL . 'facebook/oauth-end.php';
			$url = 'https://graph.facebook.com/oauth/authorize'
					. '?client_id=' . $_SESSION['application_id']
					. '&redirect_uri=' . $_SESSION['redirect_uri'];
			$url.='&scope=publish_stream'; //掲示板に投稿する場合

			$smarty->assign('facebook_url', $url);

		}else{
			$user = new user($user_id);
			if($user == NULL){
				// **add err code
			}
		}

		// ページ処理
		$page_path = PAGE_DIR . $sv_pg . ".php";
		$result = include_once ($page_path);

		if(strtoupper( $method )=="GET" || $ajax==1){
			// エラーメッセージ
			$smarty->assign('msg1', $message1);
			$smarty->assign('msg2', $message2);
			$smarty->assign('err', $err);

			// 認証SNS
			if($_SESSION['twitter_id']){
				// Twitter認証
				$smarty->assign('sns', 'twitter');
			}else if($_SESSION['facebook_id']){
				// facebook認証
				$smarty->assign('sns', 'facebook');
			}
			
			// Smartyに変数を設定
			$smarty->assign('user_id', $user_id);
			if($user != NULL){
				$smarty->assign('user_name', $user->name());
				$smarty->assign('profile_img', $user->image_url());
			}

			// テンプレート表示
			$template = $sv_pg . ".tpl";
			$smarty->display( $template );

			$_SESSION['err'] = 0;
		}elseif(strtoupper( $method ) == "POST"){
			$_SESSION['err'] = $err;
			$_SESSION['message1'] = $message1;
			$_SESSION['message2'] = $message2;

			// リダイレクト
			if($redirect_url){
				redirect($redirect_url);
			}
		}

		// コミット
		$db->commit();
		
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
