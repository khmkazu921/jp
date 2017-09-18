<!DOCTYPE html>
<html lang="ja">

	<?php

	$month = $_POST['month'];
	$foldername = "【40thつくば】" . $month . "月度デジタル精算書";	
	$dir = dirname(__FILE__) . '/tmp/' . $foldername;
	$filename = './tmp/' . $foldername . '.zip';

	zipDirectory($dir, $filename);

	function zipDirectory($dir, $file, $root=""){
		$zip = new ZipArchive();
		$res = $zip->open($file, ZipArchive::CREATE);
		
		if($res){
			// $rootが指定されていればその名前のフォルダにファイルをまとめる
			if($root != "") {
				$zip->addEmptyDir($root);
				$root .= DIRECTORY_SEPARATOR;
			}
			
			$baseLen = mb_strlen($dir);
			
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(
					$dir,
					FilesystemIterator::SKIP_DOTS
					|FilesystemIterator::KEY_AS_PATHNAME
					|FilesystemIterator::CURRENT_AS_FILEINFO
				), RecursiveIteratorIterator::SELF_FIRST
			);
			
			$list = array();
			foreach($iterator as $pathname => $info){
				$localpath = $root . mb_substr($pathname, $baseLen);
				
				if( $info->isFile() ){
					$zip->addFile($pathname, $localpath);
				} else {
					$res = $zip->addEmptyDir($localpath);
				}
			}
			
			$zip->close();
		} else {
			return false;
		}
	}
	
	require_once __DIR__ . '/vendor/autoload.php';
	session_start();
	
	$client = new Google_Client();
	$client->setAccessToken($_SESSION['token']['access_token']);          
	$service = new Google_Service_Drive($client);
	$file = new Google_Service_Drive_DriveFile();
	$file->parents = array('0B6HN57Ic_4rrZFZ2V0tTY3dNNjA');

	$file->name = $foldername . '.zip';
	$chunkSizeBytes = 1 * 1024 * 1024;
    $client->setDefer(true);
	
	$request = $service->files->create($file);
    $media = new Google_Http_MediaFileUpload(
		$client,
		$request,
		'application/zip',
		null,
		true,
		$chunkSizeBytes
	);
	$media->setFileSize(filesize($filename));
    $status = false;
	$handle = fopen($filename, "rb");
	while (!$status && !feof($handle)) {
		$chunk = readVideoChunk($handle, $chunkSizeBytes);
		$status = $media->nextChunk($chunk);
	}
	$result = false;
	if ($status != false) {
		$result = $status;
	}
	fclose($handle);
	
	function readVideoChunk ($handle, $chunkSize)
	{
		$byteCount = 0;
		$giantChunk = "";
		while (!feof($handle)) {
			$chunk = fread($handle, 8192);
			$byteCount += strlen($chunk);
			$giantChunk .= $chunk;
			if ($byteCount >= $chunkSize)
			{
				return $giantChunk;
			}
		}
		return $giantChunk;
	}

	echo var_dump($results);
	
	?>
