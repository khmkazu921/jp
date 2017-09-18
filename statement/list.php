<?php

require_once __DIR__ . '/vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setAccessToken($_SESSION['token']['access_token']);          
$service = new Google_Service_Drive($client);
$file = new Google_Service_Drive_DriveFile();
$data =  file_get_contents('sample.txt');


try {
    // 親フォルダ
    // root でマイドライブ, root 以外は名前ではなく ID を指定
    $parents = 'root';
    if (isset($_GET['parents'])) {
        $parents = htmlspecialchars($_GET['parents'], ENT_QUOTES);
    }
    // 次ページに移動する場合に渡すトークン
    $pageToken = null;
    if (isset($_GET['pageToken'])) {
        $pageToken = $_GET['pageToken'];
    }
    $parameters = array('q' => "'{$parents}' in parents");

    if ($pageToken) {
        $parameters['pageToken'] = $pageToken;
    }
	$files = $service->files->listFiles($parameters);
	// 次ページのトークン取得, ない場合は NULLここで止まる
	$results = $files->getFiles();
	$pageToken = $files->getNextPageToken();

	//echo var_dump($results);
    foreach ($results as $result) {
        if ($result->mimeType == 'application/vnd.google-apps.folder') {
            echo 'フォルダ ：'.$result->name.' ID：'.$result->id.'<br />';
        } else {
            echo 'ファイル ：'.$result->name.' ID：'.$result->id.'<br/>';
        }
    }//
	// pageToken があったら次ページヘのリンク表示
    if ($pageToken) {
        echo '<a href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?parents='.urlencode($parents).'&pageToken='.urlencode($pageToken).'">次ページ</a>';
    }
}catch(Google_Exception $e){
    echo $e->getMessage() ;
}

?>

