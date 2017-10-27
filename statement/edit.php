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
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>登録</title>
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
		<p>登録が完了しました。<br /><a href="start.php">戻る</a></p>
		<?php
		$dbh2 = connectDb();
		$item = htmlspecialchars($_POST['item']);
		$sql = "SELECT * FROM " . $item . "ORDER BY id";
		$st3 = $dbh2 -> query($sql);
		$data2 = $st3->fetchAll(PDO::FETCH_ASSOC);
		?>

	<h1>挿入</h1>
	<form action="insert.php" method="post">
		<?php
		foreach (array_keys($row) as $val) {
			echo $val;
		?>
			<br>
			<input type="text" name="<?=$val?>"><br>
		<?php
		}
		?>
		<input type="hidden" name="item" value="<?=$item?>">
		<input type="submit">
	</form>
	
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
		foreach (array_values($data2) as $row) {
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

			<?php
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
			?>
		</tr> 
		<?php
		}
		?>
		
	</table>
	<a href="setting.php">戻る</a>
	</body>
</html>

