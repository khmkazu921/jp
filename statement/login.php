<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('functions.php');
session_start();

$client = setGoogleCliant();
$service = new Google_Service_Drive($client);

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
		
		//DB内のユーザ検索
		$dbh = connectDb();
		$st = $dbh->prepare('SELECT * FROM staff WHERE staffid = ?');
		$st->execute(array($staff['sub']));

		//ユーザが存在した時のみSESSIONにstaffを追加
		if($st->fetch(PDO::FETCH_ASSOC)) {
			$_SESSION['staff'] = $staff;
			$_SESSION['client'] = $client;
			header('Location: http://'.$_SERVER['HTTP_HOST'].'/statement/start.php');
			exit;
		} else {
			echo '<a href="http://khm.extrem.ne.jp/wordpress/">つくば支部</a>の人のみ入ることができます。';
		}
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
}

//トークンがセットされていなかったら自動でリダイレクト
else {
    // 認証用URL取得
    $client->setScopes("https://www.googleapis.com/auth/userinfo.profile email");
    $authUrl = $client->createAuthUrl();
	header('Location: '. $authUrl);
}
?>

