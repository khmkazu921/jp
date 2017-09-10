<?php

header("Content-type: text/html; charset=utf-8");

$dsn = 'mysql:host=localhost;dbname=form_study;charset=utf8';
$user = 'root';
$password = 'qSJNFXBqw9Z5542D';

if(empty($_POST)) {
	echo "<a href='update.php'>update.php</a>←こちらのページからどうぞ";
	exit();
}
$dbh = new PDO($dsn, $user, $password);

if (!isset($_POST['id'])  || !is_numeric($_POST['id']) ){
	echo "IDエラー";
	exit();
}

$item = htmlspecialchars($_POST['item']);
$sql = "SELECT * FROM " . $item;
$st1 = $dbh -> query($sql);
$data = $st1->fetchAll(PDO::FETCH_ASSOC);
$row = $data[0];

$delete = "DELETE FROM " . $item . " WHERE ";
foreach (array_keys($row) as $val) {
	$delete .= $val . " = '" . $_POST[$val] . "' AND ";
}
$delete = rtrim($delete, ', AND');

$st2 = $dbh->prepare($delete);
$st2->execute();

$dbh = null;
?>

<!DOCTYPE html>
<html>
	<head>
		<title>削除</title>
	</head>
	<body>
		<h1>削除画面</h1> 
		
		<p></p>
		<?php echo $delete;?>
		削除完了しました。
		<a href="setting.php">戻る</a>
				
	</body>
</html>
