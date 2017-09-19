<?php

header("Content-type: text/html; charset=utf-8");

require_once('functions.php');		
try {
	$dbh = connectDb();
	$item = htmlspecialchars($_POST['item']);
	$sql = "SELECT * FROM " . $item;
	$st1 = $dbh -> query($sql);
	$data = $st1->fetchAll(PDO::FETCH_ASSOC);
	$row = $data[0];//キーの登録
	$param = array();
} catch (PDOException $e) {
	print('Error:'.$e->getMessage());
	die();
}

$update = "UPDATE " . $item . " SET ";
foreach (array_keys($row) as $val) {
	$post = $_POST[$val];
	echo $val;
	if(strcmp($val,'id') != 0) {	
		$update .= $val . " = :" . $val . " , ";
	}
	$param += array(":".$val => $post);
}
$update = rtrim($update, ', ') . " WHERE id = :id";//SQL文の完成

$st2 = $dbh->prepare($update);
$st2->execute($param);

$dbh = null;
?>

<!DOCTYPE html>
<html>
	<head>
		<title>変更完了</title>
	</head>
	<body>
		<h1>変更画面</h1> 
		
		<p></p>
		<?php echo $update. "\n" . var_dump($param);?>
		変更完了しました。
		<a href="setting.php">戻る</a>
	</body>
</html>
