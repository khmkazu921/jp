<?php

require_once('../Google/autoload.php');

// セッションスタート
session_start();
$client = new Google_Client();
// クライアントID
$client->setClientId('クライアントID');
// クライアントシークレット
$client->setClientSecret('クライアントシークレット');
// リダイレクトURI
$client->setRedirectUri('リダイレクトURI');

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
        echo "Google Drive Api 連携完了！<br>";
        $_SESSION['client'] = $client;
    } catch (Google_Exception $e) {
        echo $e->getMessage();
    }
} else {
    // 認証用URL取得
    $client->setScopes(Google_Service_Drive::DRIVE);
    $authUrl = $client->createAuthUrl();
    echo '<a href="'.$authUrl.'">アプリケーションのアクセスを許可してください。</a>';
    exit;
}
?>

<a href="list.php">ファイル一覧</a><br>
<a href="imageview.php">画像表示</a><br>
<a href="upload.php">アップロード</a>
