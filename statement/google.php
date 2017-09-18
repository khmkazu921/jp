<?php
/*
define('CLIENT_ID', '709072939097-hbun9dp9cjuq9q45bsoqrverfhp49esk.apps.googleusercontent.com');
define('CALLBACK_URL', 'http://localhost:8888/statement/start.php');
define('AUTH_URL', 'https://accounts.google.com/o/oauth2/auth');

$querys = array(
	'client_id' => CLIENT_ID,
	'redirect_uri' => CALLBACK_URL,
	'scope' => 'https://www.googleapis.com/auth/userinfo.profile',
	'response_type' => 'code',
);

$url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($querys);

echo "<a href='" . $url . "'>ログイン</a>";

header("Location: " . AUTH_URL . '?' . http_build_query($params));
 */

// ライブラリ読み込み
require_once __DIR__ . '/vendor/autoload.php';
// セッションスタート
session_start();
$client = new Google_Client();
// クライアントID
$client->setClientId('709072939097-hbun9dp9cjuq9q45bsoqrverfhp49esk.apps.googleusercontent.com');
// クライアントSecret ID
$client->setClientSecret('u3-EXdB_660nU5HYxCYRoaB9');
// リダイレクトURL
$client->setRedirectUri('http://localhost:8888/statement/google.php');
 
$service = new Google_Service_Drive($client);
// 許可されてリダイレクトされると URL に code が付加されている
// code があったら受け取って、認証する
if (isset($_GET['code'])) {
    // 認証
    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    // リダイレクト GETパラメータを見えなくするため（しなくてもOK）
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
    exit;
}
 
// セッションからアクセストークンを取得
if (isset($_SESSION['token'])) {
    // トークンセット
    $client->setAccessToken($_SESSION['token']);
}
 
// トークンがセットされていたら
if ($client->getAccessToken()) {
    try {
        echo "Google Drive Api 連携完了！ <a href='upload.php'>アップロード</a>";
		$_SESSION['client'] = $client;
    } catch (Google_Exception $e) {
        echo $e->getMessage();
    }
} else {
    // 認証用URL取得
    $client->setScopes(Google_Service_Drive::DRIVE);
    $authUrl = $client->createAuthUrl();
    echo '<a href="'.$authUrl.'">アプリケーションのアクセスを許可してください。</a>';
}
//unset($_SESSION['token'];
?>
