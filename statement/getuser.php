<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('functions.php');
session_start();



//リダイレクトされてきた場合に更新してトークンをセット2回目は飛ばす
if (isset($_GET['code'])) {
	$client->authenticate($_GET['code']);
	$_SESSION['token'] = $client->getAccessToken();
	header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	exit;
}

if (isset($_SESSION['token'])) {
	try {
		//ユーザ情報の収集
		$staff = getGoogleUserInfo($_SESSION['token']['access_token']);
		unset($_SESSION['token']);
		//収集できなかったらやり直す
		if(empty($staff)) {
			header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
			exit;
		}
		$staffid = $staff['sub'];
		
		//DB内のユーザ検索
		$dbh = connectDb();
		$st = $dbh->prepare('SELECT * FROM staff WHERE staffid = ?');
		$st->execute(array($staffid));

		//ユーザが存在した時のみSESSIONにstaffを追加
		if($st->fetch(PDO::FETCH_ASSOC)) {
			$_SESSION['staff'] = $staff;
			$_SESSION['client'] = $client;
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/statement/start.php');
			exit;
		}
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
}

//トークンがセットされていなかったら
else {
    // 認証用URL取得
    $client->setScopes("https://www.googleapis.com/auth/userinfo.profile email");
    $authUrl = $client->createAuthUrl();
    echo '<a href="'.$authUrl.'">経費発生報告のためにユーザ登録をします</a><br>次のページでdot-jpアカウントを選択してください';
}

?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
