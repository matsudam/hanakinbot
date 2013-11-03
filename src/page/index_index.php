<?php
	if($user_id){
		if($param["a"] == "logout"){
			/*
			 * ログアウト処理
			 */ 
			// セッションの破棄
			$_SESSION = array();
			session_destroy();
			
			$user_id = 0;
		}elseif ($param["a"] == "delete"){
			/*
			 * アカウント削除処理
			 */ 
			// userテーブルに削除フラグを立てる
			$sql = "UPDATE users A set "
								. "A.delete=1,"
								. "A.update_date=sysdate() "
								. "where A.user_id=" . $user_id . " and A.delete=0";
			$result = $db->query($sql);

			// twittersテーブルから削除
			$sql = "delete from twitters where user_id=" . $user_id;
			$result = $db->query($sql);
			
			// facebooksテーブルから削除
			$sql = "delete from facebooks where user_id=" . $user_id;
			$result = $db->query($sql);
			
			// bot_entryテーブルから削除
			$sql = "delete from bot_entry where user_id=" . $user_id;
			$result = $db->query($sql);

			$_SESSION = array();
			session_destroy();
			$user_id = 0;
		}
		$smarty->assign('a', $param["a"]);
	}
?>
