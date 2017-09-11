<?php

header("Content-type: text/html; charset=utf-8");

$dsn = 'mysql:host=localhost;dbname=form_study;charset=utf8';
$user = 'root';
$password = 'qSJNFXBqw9Z5542D';

try{
	$dbh = new PDO($dsn, $user, $password);
	$item = htmlspecialchars($_POST['item']);
	$sql = "SELECT * FROM " . $item;
	$statement = $dbh -> query($sql);
	$data = $statement->fetchAll(PDO::FETCH_ASSOC);
	$row = $data[0];
} catch (PDOException $e) {
	print('Error:'.$e->getMessage());
	die();
}
?>

<!DOCTYPE html>
<html lang="ja">
	<meta charset="UTF-8">
	<title>設定画面（表）</title>
	<body>
		<h1>挿入・変更・削除</h1>
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
			<tr>
				<form action="insert.php" method="post">
					<?php
					foreach (array_keys($row) as $val) {
					?>
						<td>
							<input type="text" name="<?=$val?>" size=10>
						</td>
					<?php
					}
					?>
					<td><input type="submit" value="挿入する"></td><td></td>
					<input type="hidden" name="item" value="<?=$item?>">
					<p>
				</form>
			</tr>			
		</table>
		<p><a href="setting.php">テーブル選択画面</a></p>
	</body>
</html>