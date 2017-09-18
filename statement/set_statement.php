<?php
header('Content-type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');

require_once('functions.php');//注意

$dbh = connectDb();
$table = array("statement","claim_to","category", "list", "place", "content_detailed", "payee");

foreach($table as $val) {
    $st = $dbh->query("SELECT * FROM " . $val);
    $data[$val] = $st->fetchAll(PDO::FETCH_ASSOC);
}

$statement_name = $data["statement"][0];

echo var_dump($data);

echo var_dump($_POST);

/*
$auto_data = array(
	"date"  => $_POST["date"],
   "payee" => $_POST["payee"],
   "content" => $_POST["content"],
   "business" => ;
   
*/
	
$insert = "INSERT INTO statement (";
foreach ($statement_name as $key => $val) {
	$insert .= $key . ", ";
}



$insert = rtrim($insert, ', ') . ")" . " VALUES (";
foreach ($statement_name as $key => $val) {
	$insert .= $val . ", ";
}

$insert = rtrim($insert, ', ') . ")";
echo var_dump($insert);

$st2 = $dbh->prepare($update);
$st2->execute($param);

$dbh = null;
?>

<?php /*

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

*/ ?>
