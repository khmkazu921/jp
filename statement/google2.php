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
$dbh = connectDb();

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
    exit;
}
if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
}
if ($client->getAccessToken()) {
    try {
        echo "Google Drive Api 連携完了！ <form action='output.php' method='post'><select name='month'><option value='8'>8月</option></select><br><input type='submit' value='作成'></form>";
		$_SESSION['client'] = $client;
    } catch (Google_Exception $e) {
        echo $e->getMessage();
    }
	$userid = getGoogleUserInfo($_SESSION['token']['access_token']);
} else {
    // 認証用URL取得
    $client->setScopes(array(Google_Service_Oauth2::PLUS_LOGIN, Google_Service_Oauth2::PLUS_ME, Google_Service_Oauth2::USERINFO_EMAIL, Google_Service_Oauth2::USERINFO_PROFILE));
    $authUrl = $client->createAuthUrl();
    echo '<a href="'.$authUrl.'">アプリケーションのアクセスを許可してください。</a>';
}

function getGoogleUserInfo($accessToken) {
    if (empty($accessToken)) {
        return null;
    }

	$q = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token='.$accessToken;
	$json = file_get_contents($q);
	$userInfo = json_decode($json,true);

	echo var_dump($userInfo);

    if (empty($userInfo)) {
        return null;
    }
	
    return $userInfo;
}

unset($_SESSION['token']);
?>
