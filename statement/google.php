<?php
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
        echo "Google Drive Api 連携完了！ <form action='output.php' method='post'><select name='month'><option value='8'>8月</option></select><input type='submit' value='作成'></form>";
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
?>
