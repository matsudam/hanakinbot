<?php
/**
 *	userクラス
 */

class user
{
	protected	$user_id;
	protected	$delete;
	protected	$mail;
	protected	$mail2;
	protected	$name;
	protected	$image_url;
	protected	$insert_date;
	protected	$update_date;
	
//	protected	$twitter_id;
//	protected	$twitter_token;				// oauth_token
//	protected	$twitter_token_secret;		// oauth_token_secret
//	protected	$twitter_name;				// screen_name

	/**
	 *コンストラクタ
	 */
	function __construct($id) {
		global $db;

		$sql = "select A.user_id
					  ,A.delete
					  ,A.mail
					  ,A.mail2
					  ,A.name
					  ,A.image_url
					  ,A.insert_date
					  ,A.update_date
				  from users A
				  where A.user_id=" . $id . " and A.delete=0";

		$result = $db->query($sql);
		$row	= $db->fetch( $result );
		// **add err code

		// メンバー変数に設定
		$this->user_id				= $row['user_id'];
		$this->delete				= $row['delete'];
		$this->mail					= $row['mail'];
		$this->mail2				= $row['mail2'];
		$this->name					= $row['name'];
		$this->image_url			= $row['image_url'];
		$this->insert_date			= $row['insert_date'];
		$this->update_date			= $row['update_date'];		
	}


	/**
	 * ユーザIDの取得
	 */
	public function id()
	{
		return $this->user_id;
	}

	/**
	 *削除フラグの取得
	 */
	public function delete()
	{
		return $this->delete;
	}

	/**
	 * メールアドレスの取得
	 */
	public function mail()
	{
		return $this->mail;
	}
	
	/**
	 * メールアドレス2の取得
	 */
	public function mail2()
	{
		return $this->mail2;
	}

	/**
	 * 名前の取得
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * プロフィール画像URLの取得
	 */
	public function image_url()
	{
		return $this->image_url;
	}

	/**
	 * 登録日時の取得
	 */
	public function insert_date()
	{
		return $this->insert_date;
	}

	/**
	 * 更新日時の取得
	 */
	public function update_date()
	{
		return $this->update_date;
	}
}
?>