<?php
session_start();
require_once('functions.php');
login_confirmation();

$staff = new Staff($_SESSION['staff']);
echo var_dump($staff);

$dbh = connectDb();
$st = $dbh->query("SELECT * FROM category");
$data = $st->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
?>
<!DOCTYPE html>
<html lang="ja">

	
<head>
	<meta charset="UTF-8">
	<title>りっぴーくん</title>
	<script type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script>	 
	 function selectCategory(category_list) {
		 var text;
		 var hidden;
		 for(var val of category_list) {
			 text += "<option value=\"" + val.id + "\">" + val.name +  "</option>";
		
		 }
		 document.getElementById('category').insertAdjacentHTML("beforeend",text);
	 }
	 	 
	 jQuery(document).ready(function($) {//ロードされた時に処理を行う
		 var data = <?=json_encode($data)?>;
		 selectCategory(data);
	 }); 
	</script>
	
</head>

<body>
	<h1>経費発生報告：入力型</h1>
	<form id="statement" method="post" action="index.php">
		カテゴリー</br>
		<select name="category" id="category"></select></br>
		精算先</br>
		<select name="bill_to">
			<option value="未来">未来</option>
			<option value="つくば">つくば</option>
		</select></br>
		名前</br>
		<input type="text" name="staff" value="<?=$staff->name?>"></br>
		<input type="submit" value="スタート">
	</form>
	<br>
	<a href="google.php">サインイン</a></br>
	<a href="login.php">ログイン</a><br>
	<a href="setting.php">設定</a></br>
	<p>
		精算書自動作成</br>
		<form action='output.php' method='post'>
			<select name='month'>
				<option value='8'>8月</option>
				<option value='9'>9月</option>
				<option value='10'>10月</option>
				<option value='11'>11月</option>
				<option value='12'>12月</option>
				<option value='1'>1月</option>
				<option value='2'>2月</option>
				<option value='3'>3月</option>
				<option value='4'>4月</option>
				<option value='5'>5月</option>
				<option value='6'>6月</option>
				<option value='7'>7月</option>
			</select><br>
			<input type='submit' value='作成'>
		</form>
	</p>
</body>
</html>
