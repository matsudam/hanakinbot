<?php

include_once (LIB_DIR . "db_exception.php");

/**
	データベースクラス
*/
class Database
{
	protected	$url;
	protected	$user;
	protected	$pass;
	protected	$db;

	protected	$link;
	protected	$sdb;

	/**
	コンストラクタ
	*/
	function __construct($url, $db, $user, $pass) {
		// DBへの接続
		$this->url = $url;
		$this->db = $db;
		$this->user = $user;
		$this->pass = $pass;
	}


	/**
	データベースの接続
	*/
	public function connect()
	{

		// MySQLへ接続する
		$this->link = mysql_connect($this->url, $this->user, $this->pass);
		if(!$this->link) throw new DbException("データベースへの接続に失敗しました。");

		// データベースを選択する
		$this->sdb = mysql_select_db($this->db, $this->link);
		if(!$this->sdb) throw new DbException("データベースの選択に失敗しました。");
	}


	/**
	データベースの接続を閉じる
	*/
	public function close()
	{
		// MySQLへの接続を閉じる
		mysql_close($this->link) or die("MySQL切断に失敗しました。");

	}

	/**
	クエリーの送信
	*/
	public function query( $sql )
	{
		$result = mysql_query( $sql, $this->link );
		// 下記の行はあとでコメントアウトする。throwは呼び出し元で行うように実装変更する予定。
		if(!$result) throw new DbException("クエリの送信に失敗しました。<br />SQL:".$sql);
		return $result;
	}


	/**
	件数の取得
	*/
	public function count( $result )
	{
		return mysql_num_rows( $result );
	}


	/**
	1件ずつ取得
	*/
	public function fetch( $result )
	{
		return mysql_fetch_assoc( $result );
	}

	/**
	InsertしたAutoIncrementで振られたIDを取得
	*/
	public function insert_id()
	{
		return mysql_insert_id();
	}

	/**
	トランザクション開始
	*/
	public function begin()
	{
		$sql = "begin";
		$result = mysql_query( $sql, $this->link ) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
	}


	/**
	コミット！
	*/
	public function commit()
	{
		$sql = "commit";
		$result = mysql_query( $sql, $this->link ) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
	}


	/**
	ロールバック
	*/
	public function rollback()
	{
		$sql = "commit";
		$result = mysql_query( $sql, $this->link ) or die("クエリの送信に失敗しました。<br />SQL:".$sql);
	}


	/**
	エスケープ
	*/
	static public function escape_string( $str )
	{
		return mysql_real_escape_string( $str );
	}


	/**
	結果保持用メモリを開放
	（スクリプト実行のメモリの使用量が多すぎると懸念される場合にのみ必要）
	*/
	public function free( $result )
	{
		return mysql_free_result( $result );
	}

}
?>
