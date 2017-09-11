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

$update = "UPDATE " . $item . " SET ";
foreach (array_keys($row) as $val) {
	$post = $_POST[$val];
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
		
		<p>変更完了しました。</p>

		<table border='1'>
			
				<tr>
					<?php
					foreach (array_keys($row) as $val) {
					?>
						<td>
							<?=$val?>
						</td>
					<?php
					}
					?>
					<td>変更</td><td>削除</td>
				</tr>
			<?php
			foreach (array_values($data) as $row) {
			?>
				<?php
				foreach (array_values($row) as $val) {
				?>
					<td>
						<?php
						echo $val;
						?>
					</td>
				<?php
				}
				?>
				<td>
					<form action="update.php" method="post">
						<input type="submit" value="変更する">

						<?php
						$f = "<input type=\"hidden\" name=\"";
						$m = "\" value = \"";
						$b = "\">";
						foreach ($row as $key2 => $val2) {
							echo $f . $key2 . $m . $row[$key2] . $b;
						}							   
						$dbh = null;
						?>
						<input type="hidden" name="item" value="<?=$item?>">
						
					</form>
				</td>
				<td>
					<form action="delete.php" method="post">
						<input type="submit" value="削除する">
						<input type="hidden" name="id" value="<?=$row["id"]?>">
						<input type="hidden" name="item" value="<?=$item?>">
						<input type="hidden" name="name" value="<?=$row["name"]?>">
					</form>
				</td>
				</tr> 
			<?php
			}
			?>
			
		</table></br>


		<a href="setting.php">戻る</a>
	</body>
	
	
</html>

