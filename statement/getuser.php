<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('functions.php');
session_start();
$client = new Google_Client();
$client->setClientId('709072939097-hbun9dp9cjuq9q45bsoqrverfhp49esk.apps.googleusercontent.com');
$client->setClientSecret('u3-EXdB_660nU5HYxCYRoaB9');
$client->setRedirectUri('http://localhost:8888/statement/getuser.php');
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
			echo "既に登録されています<br>タブを閉じても大丈夫です";
			exit;
		} else {
			$st2 = $dbh->prepare("INSERT INTO staff (name, staffid, mail) VALUES (:name, :staffid, :mail)");
			$st2->bindParam(':name', $staff['name'], PDO::PARAM_STR);
			$st2->bindParam(':staffid', $staffid, PDO::PARAM_STR);
			$st2->bindParam(':mail', $staff['email'], PDO::PARAM_STR);
			$st2->execute();
			echo "登録完了<br>タブを閉じても大丈夫です";
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

