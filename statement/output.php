<?php
session_start();
require_once('functions.php');		

require_once(dirname(__FILE__) . '/PHPExcel/PHPExcel.php');
require_once(dirname(__FILE__) . '/PHPExcel/PHPExcel/IOFactory.php');

header('Content-type: text/html; charset=UTF-8');

try{
	$dbh = connectDb();
	$month = 8;
	$m = '2017-' . sprintf('%02d', $month);
	$f = $m . '-01';
	$b = date('Y-m-d', strtotime('last day of ' . $m));
	$sql = "SELECT distinct staff FROM statement WHERE date BETWEEN '".$f."' AND '".$b."'";

	$st = $dbh->query($sql);
	$staff_list = $st->fetchAll(PDO::FETCH_ASSOC);

	$sql = "SELECT distinct bill_to FROM statement WHERE date BETWEEN '".$f."' AND '".$b."'";	
	$st = $dbh->query($sql);
	$bill_to_list = $st->fetchAll(PDO::FETCH_ASSOC);

	$zip = "./tmp/" . "【40thつくば】" . $month ."月度デジタル精算書/";

	if (!file_exists($zip)) {
		if (!mkdir($zip, 0777, true)) {
			die('Failed to create zip folders...');
		}
	}

	foreach($bill_to_list as $bill_to) {
		$str = $zip . "【40th" . $bill_to['bill_to'] . "】" . $month . "月度デジタル精算書/";
		if (!file_exists($str)) {
			if (!mkdir($str, 0777, true)) {
				die('Failed to create folders...');
			}
		}

		foreach($staff_list as $staff) {
			$sql = "SELECT * FROM statement WHERE staff='" . $staff['staff'] . "' AND date>'".$f."' AND date<'".$b."'";
			$st = $dbh->query($sql);
			$data = $st->fetchAll(PDO::FETCH_ASSOC);
			$filepath = dirname(__FILE__) . "/sample.xlsx";
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');
			$book = $objReader->load($filepath);
			$book->setActiveSheetIndex(0);
			$sheet = $book->getSheetByName("NO.1");
			$sheet->setCellValue('I2', $bill_to['bill_to']);
			$sheet->setCellValue('J2', $staff['staff']);
			$i = 0;
			foreach($data as $val){				
				$row = $i + 6;
				$sheet->setCellValue('C'.$row, intval(substr($val['date'],5,2)));
				$sheet->setCellValue('D'.$row, intval(substr($val['date'],8,2)));
				$sheet->setCellValue('E'.$row, $val['payee']);
				$sheet->setCellValue('F'.$row, $val['name']);
				$sheet->setCellValue('G'.$row, $val['business']);
				$sheet->setCellValue('I'.$row, $val['business_detailed']);
				$sheet->setCellValue('J'.$row, $val['account']);
				$sheet->setCellValue('K'.$row, $val['cost']);
				$i++;
			}
			
			header('Content-Type: application/octet-stream');
			header('Cache-Control: max-age=0');
			
			$writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
			$file_name = "【39th".$bill_to['bill_to']."】".$staff['staff']."＠".$month."月分精算書.xlsx";
			$writer->save($str . $file_name);
			//exit;
			
		}	
	}
	
} catch (PDOException $e) {
	print('Error:'.$e->getMessage());
	die();
}


?>




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
