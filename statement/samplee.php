<?php
 
require_once(dirname(__FILE__) . '/PHPExcel/PHPExcel.php');
require_once(dirname(__FILE__) . '/PHPExcel/PHPExcel/IOFactory.php');
 
$filepath = "template.xlsx";
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$book = $objReader->load($filepath);

$book->setActiveSheetIndex(0);
//$sheet = $book->getActiveSheet();
$sheet = $book->getSheetByName("NO.1");

//$excel = new PHPExcel();
//$excel->setActiveSheetIndex(0);
//$sheet = $excel->getActiveSheet();
//$sheet->setTitle('Sheet1');
$sheet->setCellValue('C6', '8');
$sheet->setCellValue('D6', '20');
$sheet->setCellValue('E6', '首都圏新都市交通株式会社');
$sheet->setCellValue('I2', '【つくば】');
$sheet->setCellValue('J2', '古川 和輝');
$sheet->setCellValue('F6', '実地フォロー＠飯島議員事務所 交通費' . PHP_EOL . '（つくば⇔守谷）');
$sheet->setCellValue('G6', 'インターン');
$sheet->setCellValue('I6', '実地フォロー');
$sheet->setCellValue('J6', '交通費');
$sheet->setCellValue('K6', '1040');

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment;filename="【39thつくば】古川和輝＠8月分精算書.xlsx"');
header('Cache-Control: max-age=0');
 
$writer = PHPExcel_IOFactory::createWriter($book, 'Excel2007');
$writer->save('php://output');
?>
