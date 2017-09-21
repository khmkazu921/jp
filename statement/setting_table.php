<?php
header("Content-type: text/html; charset=utf-8");
require_once('functions.php');		

login_confirmation();

try{
$dbh = connectDb();
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
		<?php echo $sql; ?>
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
							?>
							<input type="hidden" name="item" value="<?=$item?>">
							
						</form>
					</td>
					<td>
						<form action="delete.php" method="post">
							<input type="submit" value="削除する">
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
			</tr> 
			<?php
			}
			?>
		</table>
		<a href="setting.php">戻る</a>
	</body>
</html>

