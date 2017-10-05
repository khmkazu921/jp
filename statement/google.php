<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('functions.php');
session_start();
$client = new Google_Client();
$client->setClientId('709072939097-hbun9dp9cjuq9q45bsoqrverfhp49esk.apps.googleusercontent.com');
$client->setClientSecret('u3-EXdB_660nU5HYxCYRoaB9');
$client->setRedirectUri('http://localhost:8888/statement/google.php');
$service = new Google_Service_Drive($client);

//リダイレクトされてきた場合に更新してトークンをセット
//2回目は飛ばす
if (isset($_GET['code'])) {
	$client->authenticate($_GET['code']);
	$_SESSION['token'] = $client->getAccessToken();
	header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
	exit;
}

//ログインが未完成な気がする
//トークンがセットされていたら
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
			header('Location: http://'.$_SERVER['HTTP_HOST']."/statement/start.php");
			exit;
		}
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
}

//トークンがセットされていなかったら
else {
    // 認証用URL取得
    $client->setScopes(Google_Service_Oauth2::USERINFO_PROFILE);
    $authUrl = $client->createAuthUrl();
    echo '<a href="'.$authUrl.'">アプリケーションのアクセスを許可してください。</a>';
}


?>

