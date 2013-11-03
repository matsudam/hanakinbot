<?php
	try{
		session_start();

		include_once ('../../src/initialization.php');
		include_once (LIB_DIR . 'database.php');
		include_once (LIB_DIR . 'common.php');

		if (isset($_GET['code'])) {
			$url = 'https://graph.facebook.com/oauth/access_token'
					. '?client_id=' . $_SESSION['application_id']
					. '&redirect_uri=' . $_SESSION['redirect_uri']
					. '&client_secret=' . $_SESSION['application_secret']
					. '&code=' . $_GET['code'];

			//HTTP_RequestはProxy経由のSSLに対応していないから使えない
			require_once 'HTTP/Request2.php';
			$request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET);
			$request->setConfig(array(
				//'proxy_host' => 'proxy.example.net',
				//'proxy_port' => 3128,
				'ssl_verify_peer' => false
				));
			$response = $request->send();
			if ($response->getStatus() / 100 != 2) {
				echo '<pre>' . $response->getReasonPhrase() . '</pre>';
				die();
			}
			parse_str($response->getBody(), $params);
			$_SESSION['access_token'] = $params['access_token'];

			$graph_url = "https://graph.facebook.com/me?access_token=" . $_SESSION['access_token'];
			$request = new HTTP_Request2($graph_url, HTTP_Request2::METHOD_GET);
			$request->setConfig(array(
				//'proxy_host' => 'proxy.example.net',
				//'proxy_port' => 3128,
				'ssl_verify_peer' => false
				));
			$response = $request->send();
			if ($response->getStatus() / 100 != 2) {
				echo '<pre>' . $response->getReasonPhrase() . '</pre>';
				die();
			}

			$params2 = json_decode($response->getBody() );
			//var_dump($params2);

			$_SESSION['facebook_id'] = $params2->id;
			$_SESSION['facebook_name'] = $params2->name;
			$_SESSION['facebook_link'] = $params2->link;

			// DBに更新
			// DBクラスの生成
			$db = new Database(DB_URI, DB_NAME, DB_USER, DB_PASS);
			// MySQLへ接続する
			$db->connect();

			// トランザクション開始
			$db->begin();

			$sql = "select user_id from facebooks A where A.facebook_id='" . $_SESSION['facebook_id'] . "'";
			$result = $db->query($sql);
			$count = $db->count($result);

			if($count == 0){
				// 新規登録
				// usersテーブルに追加
				$sql = "INSERT INTO users (name,image_url,insert_date,update_date) ";

				$values = "VALUES(" . "'" . $params2->name . "',"
									. "'https://graph.facebook.com/". $_SESSION['facebook_id'] ."/picture?type=square',"
									. "sysdate(),"
									. "sysdate()"
								. ");";

				$sql = $sql . $values;
				$result = $db->query( $sql );

				$user_id = $db->insert_id();

				// facebooksテーブルに追加
				$sql = "INSERT INTO facebooks (facebook_id, user_id, access_token, name, link, insert_date,update_date) ";

				$values = "VALUES(" . "'" . $_SESSION['facebook_id'] . "',"
									. "'" . $user_id . "',"
									. "'" . $params['access_token'] . "',"
									. "'" . $_SESSION['facebook_name'] . "',"
									. "'" . $_SESSION['facebook_link'] . "',"
									. "sysdate(),"
									. "sysdate()"
								. ");";

				$sql = $sql . $values;
				$result = $db->query( $sql );

				// bot_entryテーブルに追加
				$sql = "INSERT INTO bot_entry (user_id,bot_id,insert_date) ";
				$values = "VALUES(" . $user_id . ","
									. "1,"
									. "sysdate()"
								. ");";
				$sql = $sql . $values;
				$result = $db->query($sql);
			}else{
				// 更新
				$row = $db->fetch( $result );
				$user_id = $row['user_id'];

				$sql = "UPDATE facebooks A set "
									. "name='" . $_SESSION['facebook_name'] . "',"
									. "link='" . $_SESSION['facebook_link'] . "',"
									. "update_date=sysdate() "
									. "where A.facebook_id='" . $_SESSION['facebook_id'] . "'";

				$result = $db->query($sql);
			}

			// コミット
			$db->commit();

			// user_id をセッションに設定
			$_SESSION['user_id'] = $user_id;

			// リダイレクト
			redirect(HOME_URL);

		}else {
			// アプリ承認で拒否されたと見なして処理しないでリダイレクト
			redirect(HOME_URL);
		}

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
