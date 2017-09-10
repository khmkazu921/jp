<?php

header("Content-type: text/html; charset=utf-8");

$dsn = 'mysql:host=localhost;dbname=form_study;charset=utf8';
$user = 'root';
$password = 'qSJNFXBqw9Z5542D';

try{
	$dbh = new PDO($dsn, $user, $password);
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
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>補足設定</title>
	</head>
	<body>
		
		<?php
		$insert = "INSERT INTO " . $item . " (";
		foreach (array_keys($row) as $val) {
			$insert .= $val . ", ";
		}
		$insert = rtrim($insert, ', ') . ") VALUES (";
		
		foreach (array_keys($row) as $val) {
			$post = $_POST[$val];
			$val = ":" . $val;
			$param += array($val => $post);//DBへ値の代入
			$insert .= $val . ", ";
		}
		
		$insert = rtrim($insert, ', ') . ")";//INSERT文完成
		
		$st2 = $dbh->prepare($insert);
		$st2->execute($param);

		$dbh = null;
		?>
		<p>登録が完了しました。<br /><a href="setting.php">戻る</a></p>
	</body>
</html>

