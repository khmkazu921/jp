<?php
header("Content-type: text/html; charset=utf-8");
require_once('functions.php');

if(empty($_POST)) {
	echo "<a href='update.php'>update.php</a>←こちらのページからどうぞ";
	exit();
}

$dbh = connectDb();

if (!isset($_POST['id'])  || !is_numeric($_POST['id']) ){
	echo "IDエラー";
	exit();
}

$item = htmlspecialchars($_POST['item']);
$sql = "SELECT * FROM " . $item;
$st = $dbh -> query($sql);
$data = $st->fetchAll(PDO::FETCH_ASSOC);
$row = $data[0];

?>

<!DOCTYPE html>
<html>
	<head>
		<title>変更</title>
	</head>
	<body>
		<h1>変更画面</h1> 
		
		<p><?php //echo $name;?></p>
		
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
			<p><input type="submit" value="変更する"></p>
		</form>
		<a href="setting.php">戻る</a>
				
	</body>
</html>
