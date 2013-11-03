<?php
	$dir = dirname(__FILE__);
	if(stristr($dir, "test")){
		// DB
		define('DB_URI'  , 'localhost');
		define('DB_NAME' , 'hanakinbot_test');
		define('DB_USER' , 'hanakinbot_test');
		define('DB_PASS' , 'mzsv3X2P87pNyA9u');

		// Twitter API関連
		define ('TWITTER_CONSUMER_KEY'		, '911');
		define ('TWITTER_CONSUMER_SECRET'	, '912');

		// facebook API関連
		define ('FACEBOOK_APP_ID'			, '921');
		define ('FACEBOOK_APP_SECRET'		, '922');
		define('FACEBOOK_ACCESS_TOKEN'		, '923');

		// Path
		define('DOC_ROOT', '/var/www/test/hanak.in/bot/');
	}else{
		// DB
		define('DB_URI'  , 'localhost');
		define('DB_NAME' , 'hanakinbot');
		define('DB_USER' , 'hanakinbot');
		define('DB_PASS' , 'SV8WD5P4jxUjA8mc');

		// Twitter API関連
		define ('TWITTER_CONSUMER_KEY'		, '11');
		define ('TWITTER_CONSUMER_SECRET'	, '12');

		// facebook API関連
		define ('FACEBOOK_APP_ID'			, '21');
		define ('FACEBOOK_APP_SECRET'		, '22');
		define('FACEBOOK_ACCESS_TOKEN'		, '23');

		// Path
		define('DOC_ROOT', '/var/www/site/hanak.in/bot/');
	}

	// パス設定
	define ('HOEM_DIR'    ,DOC_ROOT . 'html/');
	define ('SRC_DIR'     ,DOC_ROOT . 'src/');
	define ('LIB_DIR'     ,SRC_DIR . 'lib/');
?>
