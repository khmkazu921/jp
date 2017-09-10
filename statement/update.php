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

$dbh = null;
?>

<!DOCTYPE html>
<html>
	<head>
		<title>変更</title>
	</head>
	<body>
		<h1>変更画面</h1> 
		
		<?php echo $name;?>
		<form action="update_regist.php" method="post">
		<?php
			$f = "</br><input type=\"text\" name=\"";
			$m = "\" value = \"";
			$b = "\"></br>";
			foreach (array_keys($row) as $val) {
				echo $val . $f . $val . $m . $_POST[$val]. $b;
			}							   
			$dbh = null;
			?>
			
			<input type="hidden" name="item" value="<?=$item?>">
			<input type="submit" value="変更する">
		</form>
		<a href="setting.php">戻る</a>
				
	</body>
</html>
