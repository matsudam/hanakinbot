<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="{$css_url}style.css" type="text/css" />
	<LINK REL="SHORTCUT ICON" HREF="/img/favicon.ico" />
	<title>{$service_name}</title>

	<!--[if IE]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js">
	</script>
	<![endif]-->

	<script type="text/javascript">
	<!--

	function disp(){

		// 「OK」時の処理開始 ＋ 確認ダイアログの表示
		if(window.confirm('削除すると今後「今日は花金」とつぶやかれなくなります。よろしいですか？')){

			location.href = "/?a=delete"; // 削除処理へ

		}else{
			// キャンセル時は処理なし
		}

	}

	// -->
	</script>

</head>
<body>

<!-- GoogleAnalytics-Start -->
{literal}
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-28045860-2', 'hanak.in');
  ga('send', 'pageview');
</script>
{/literal}
<!-- GoogleAnalytics-end -->

<div id="main">
<img src="/img/title.png" />

{if $user_id == 0}
	{if $a == 'logout'}
		<p>ログアウトしました。</p>
		<a href="/">トップへ</a>
	{else if $a == 'delete'}
		<p>アカウントを削除しました。</p>
		<a href="/">トップへ</a>
	{else}
		<div class="message">
			<p>
			本サービスに登録いただくと毎週金曜日の夕方に「今日は花金♪」みたいなコトを、あなたに代わってつぶやきます。<br />
			以下のボタンからつぶやきたいアカウントでログインしてください。<br />
			現在はTwitterとfacebookに対応しています。
			</p>
		</div>
		<div id="btn" class="clearfix">
			<div id="btn_left">
				<a href='{$twitter_url}'><img src="/img/twitter_btn.png" /></a>
			</div>
			<div id="btn_right">
				<a href='{$facebook_url}'><img src="/img/facebook_btn.png" /></a>
			</div>
		</div>
	{/if}
{else}
	<p><img src="{$profile_img}"></p>
	<p><span id="welcom">{$user_name}さん、ようこそ！</span></p>
	<p>{$sns} アカウントでログインしています。</p>

	<p id="logout"><a href="/?a=logout"><img src="/img/logout.png" /></a></p>
	<p id="delete"><a href="javascript:void(0);" onclick="disp();"><img src="/img/delete.png" /></a></p>
{/if}
</div>
<footer id="footer">
	Copyright (C) 2011-{$smarty.now|date_format:"%Y"} <a href="http://matsudam.com" target="_blank">matsudam</a> All Rights Reserved.
</footer><!-- #footer -->

</body>
</html>