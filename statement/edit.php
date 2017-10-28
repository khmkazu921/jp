<?php
header("Content-type: text/html; charset=utf-8");
require_once('functions.php');		
$dbh = connectDb();
$item = htmlspecialchars($_POST['item']);
$sql = "SELECT * FROM " . $item;
$st1 = $dbh -> query($sql);
$data = $st1->fetchAll(PDO::FETCH_ASSOC);
$row = $data[0];//キーの登録
$param = array();
$user = $_POST['user'];
/*} catch (PDOException $e) {
	print('Error:'.$e->getMessage());
	die();
}*/
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="css/buttons.css">
		<link rel="stylesheet" href="css/style.css">
		<title>登録</title>
	</head>
	<body>

		<h1>経費発生報告フォーム</h1>
		<?		
		$type = $_POST['type'];
		if($type == "insert") {
			$insert = "INSERT INTO " . $item . " (";
			foreach (array_keys($row) as $val) {
				$insert .= $val . ", ";
			}
			$insert = rtrim($insert, ', ') . ") VALUES (";
			
			foreach (array_keys($row) as $val) {
				$val = ":" . $val;
				$param += array($val =>  $_POST[$val]);//DBへ値の代入
				$insert .= $val . ", ";
			}			
			$insert = rtrim($insert, ', ') . ")";//INSERT文完成
			$st2 = $dbh->prepare($insert);
			$st2->execute($param);
		}
		else if ($type == "delete") {
			$sql = "SELECT * FROM " . $item;
			$st1 = $dbh -> query($sql);
			$data = $st1->fetchAll(PDO::FETCH_ASSOC);
			$row = $data[0];			
			$delete = "DELETE FROM " . $item . " WHERE id=" . $_POST['id'];
/*			foreach (array_keys($row) as $val) {
				$delete .= $val . " = '" . $_POST[$val] . "' AND ";
			}*/
			$delete = rtrim($delete, ', AND');
			$st2 = $dbh->prepare($delete);
			$st2->execute();
		}
		else if ($type == "update") {
			$update = "UPDATE " . $item . " SET ";
			foreach (array_keys($row) as $val) {
				if(strcmp($val,'id') != 0) {
					
					$update .= $val . " = :" . $val . " , ";
				}
				$param += array(":".$val => $_POST[$val]);
			}
			$update = rtrim($update, ', ') . " WHERE id = :id";	
			$st2 = $dbh->prepare($update);
			$st2->execute($param);
		}
		else if ($type == "copy") {
			$insert = "INSERT INTO " . $item . " (";
			foreach (array_keys($row) as $val) {
				if ($val != 'id')
					$insert .= $val . ", ";
				$insert = rtrim($insert, ', ') . ") VALUES (";
				foreach (array_keys($row) as $val) {
					if ($val != 'id') {
						$post = $_POST[$val];
						$val = ":" . $val;
						$param += array($val => $post);//DBへ値の代入
						$insert .= $val . ", ";
					}
				}
				$insert = rtrim($insert, ', ') . ")";//INSERT文完成
				//echo var_dump($param);
				$st2 = $dbh->prepare($insert);
				$st2->execute($param);
			}
		}
			//else { }
			//$dbh = null;
		?>
		<p>登録が完了しました。<br /><a href="start.php">戻る</a></p>

		<?php
		$a = isset($_POST['disp_all']);
		if($a) echo '<h2>全員分の精算書リスト</h2>';
		else echo '<h2>' . $user . "の分の精算書リスト" . '</h2>'; 
		//		$dbh2 = connectDb();
		$item = htmlspecialchars($_POST['item']);
		$sql = (isset($_POST['disp_all'])) ?
			   "SELECT * FROM " . $item . " ORDER BY date DESC"
			 : "SELECT * FROM ".$item." WHERE staff='".$user."' ORDER BY date DESC";
		$st3 = $dbh -> query($sql);
		$data2 = $st3->fetchAll(PDO::FETCH_ASSOC);
		?>

		<form action="edit.php" method="post">
			<input type="submit" value="<?=$a?$user.'の':'全員'?>分を表示">
			<?php
			$f = "<input type='hidden' name='";
			$m = "' value = '";
			$b = "'>";
			foreach ($row as $key2 => $val2) {
				echo $f . $key2 . $m . $row[$key2] . $b;
			}
			//			$dbh = null;
			?>
			<input type="hidden" name="item" value="<?=$item?>">
			<input type="hidden" name="staff" value="<?=$staff?>">
			<input type="hidden" name="user" value="<?=$user?>">
			<input type="hidden" name="id" value="<?=$row["id"]?>">
			<?=$a?"":"<input type='hidden' name='disp_all' value='1'>"?>
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
				<td>変更</td><td>複製</td><td>削除</td>
			</tr>
			<tr>
				<form action="edit.php" method="post">
					<?php
					foreach (array_keys($row) as $val) {
						if($val == 'id') echo '<td></td>';
						else {
					?>
						<td>
							<input type="text" name="<?=$val?>" size='<?=$val == 'name'? '30' : '15'?>'>
						</td>
					<?php
					}
					}
					?>
					<input type="hidden" name="item" value="<?=$item?>">
					<input type="hidden" name="type" value="insert">
					<?=$a?"<input type='hidden' name='disp_all' value='1'>":""?>
					<td>
						<input type="submit">
					</td>
					<td>
						<input type="reset">
					</td><td></td>
				</form>
			</tr>

			<?php
			foreach (array_values($data2) as $row) {
					foreach (array_values($row) as $val) {
			?>
				<td><?=$val?></td>
			<?php
			}
			?>
			<td>
				<form action="update.php" method="post">
					<input type="submit" value="編集">
					<?php
					$f = "<input type='hidden' name='";
					$m = "' value = '";
					$b = "'>";
					foreach ($row as $key2 => $val2)
					echo $f . $key2 . $m . $row[$key2] . $b;							   						?>
					<input type='hidden' name='item' value='<?=$item?>'>
					<input type='hidden' name='type' value='edit'>
					<?=$a?"<input type='hidden' name='disp_all' value='1'>":""?>
					<input type="hidden" name="user" value="<?=$user?>">
				</form>
			</td>
			<td>
				<form action="edit.php" method="post">
					<input type="submit" value="コピー">
					<?php
					$f = "<input type='hidden' name='";
					$m = "' value = '";
					$b = "'>";
					foreach ($row as $key2 => $val2) {
						echo $f . $key2 . $m . $row[$key2] . $b;
					}							   
					$dbh = null;
					?>
					<input type="hidden" name="item" value="<?=$item?>">
					<input type="hidden" name="type" value="copy">
					<?=$a?"<input type='hidden' name='disp_all' value='1'>":""?>
					<input type="hidden" name="user" value="<?=$user?>">
				</form>
			</td>
			<td>
				<form action="edit.php" method="post">
					<input type="submit" value="削除">
					<?php
						$f = "<input type='hidden' name='";
						$m = "' value = '";
						$b = "'>";
						foreach ($row as $key2 => $val2) {
							echo $f . $key2 . $m . $row[$key2] . $b;
						}							   
						$dbh = null;
						?>
						<input type="hidden" name="item" value="<?=$item?>">
						<input type="hidden" name="type" value="delete">
						<input type="hidden" name="id" value="<?=$row["id"]?>">
						<?=$a?"<input type='hidden' name='disp_all' value='1'>":""?>
						<input type="hidden" name="user" value="<?=$user?>">
					</form>
				</td>
			</tr> 
			<?php
			}
			?>
		</table>
		<a href="setting.php">戻る</a>
	</body>
</html>

