<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once('functions.php');
session_start();
$client = new Google_Client();
$client->setClientId('709072939097-hbun9dp9cjuq9q45bsoqrverfhp49esk.apps.googleusercontent.com');
$client->setClientSecret('u3-EXdB_660nU5HYxCYRoaB9');
$client->setRedirectUri('http://localhost:8888/statement/google.php');
$service = new Google_Service_Drive($client);
$status = "none";
echo $status;

//リダイレクトされてきた場合に作動
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
    exit;
}

//トークンがセットされていたら
if (isset($_SESSION['token'])) {
	try {
		$status = "token_is_set"
		$client->setAccessToken($_SESSION['token']);	
		$userInfo = getGoogleUserInfo($_SESSION['token']['access_token']);
		$staffid = $userInfo->id;

		//ユーザの検索
		$dbh = connectDb();
		$st = $pdo->prepare('SELECT * FROM staff WHERE id = ?');
		$st->execute(array($staffid));

		//ユーザが存在したら
		if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$_SESSION['staff'] = $userInfo->name;
			$_SESSION['staffid'] = $staffid;
			$_SESSION['mail'] = $userInfo->mail;
			$_SESSION['client'] = $client;
			if(isset($_SESSION["userid"])) {
				$status = "logged_in";
			}
			echo var_dump($_SESSION);
			$status = "user_is_exist";
		}
		
		$status = "failed";
	
//		echo "Google Drive Api 連携完了！ 精算書自動作成>></br><form action='output.php' method='post'><select name='month'><option value='8'>8月</option></select><br><input type='submit' value='作成'></form>";
		//重要！！消さない！
		
		//アクセストークンを得る
    } catch (Google_Exception $e) {
        echo $e->getMessage();
    }
} else {
    // 認証用URL取得
    $client->setScopes(array(/*Google_Service_Oauth2::PLUS_LOGIN, Google_Service_Oauth2::PLUS_ME, Google_Service_Oauth2::USERINFO_EMAIL, */Google_Service_Oauth2::USERINFO_PROFILE));
    $authUrl = $client->createAuthUrl();
    echo '<a href="'.$authUrl.'">アプリケーションのアクセスを許可してください。</a>';
}

function getGoogleUserInfo($accessToken) {
    if (empty($accessToken)) return null;
	$q = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token='.$accessToken;
	$json = file_get_contents($q);
	$userInfo = json_decode($json,true);
    if (empty($userInfo)) {
        return null;
    }
    return $userInfo;
}

unset($_SESSION['token']);
?>
