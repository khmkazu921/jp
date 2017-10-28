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
		<form action="edit.php" method="post">
			<table border='1'>
				<tr>
					<?php
					foreach (array_keys($row) as $val) {
					?>
						<td><?=$val?></td>
					<?php
					}
					?>
				</tr>
				<tr>
					<?php
					foreach (array_keys($row) as $val) {
						if($val == 'id') {
					?>	
						<td><?=$_POST[$val]?></td>
						<input type='hidden' name='id' value='<?=$_POST[$val]?>'>
					<?php
					} else {
					?>	
						<td>
							<input type='text' name='<?=$val?>' value='<?=$_POST[$val]?>'>
						</td>
					<?php
					}
					}
					?>
					<input type="hidden" name="item" value="<?=$item?>">
					<input type='hidden' name='user' value='<?=$_POST['user']?>'>
					<input type='hidden' name='type' value='update'>
				</tr>
			</table>
			<p><input type="submit" value="変更する"></p>
		</form>
		<a href="setting.php">戻る</a>
	</body>
</html>
