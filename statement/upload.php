<!DOCTYPE html>
<html lang="ja">

<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setAccessToken($_SESSION['token']['access_token']);          
$service = new Google_Service_Drive($client);
$file = new Google_Service_Drive_DriveFile();
$data =  file_get_contents('sample.xlsx');

$file->parents = array('0B6HN57Ic_4rrZFZ2V0tTY3dNNjA');

echo "</br><a href='list.php'>list</a></br>";

$results = $service->files->create($file, array(
	'data' => $data,
	'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	'uploadType' => 'media'
));

echo var_dump($results);
?>
