<?php
session_start();
require_once('functions.php');
//login_confirmation();

$staff = new Staff($_SESSION['staff']);

$dbh = connectDb();
$st2 = $dbh->query("INSERT INTO staff FROM category");
$st = $dbh->query("SELECT * FROM category");
$data = $st->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/buttons.css">
	<title>経費発生報告フォーム</title>
	<script type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

	<script>	 
	 function selectCategory(catli) {
		 var text = "";
		 var hidden;
		 var i = 0;
		 for(var val of catli) {
			 text += " <input type='radio' name='category' id='" + val.id + "' value='"
				   + val.id + "'/><label class='radio_btn' for='" + val.id + "'>"
				   + val.name + "</label> ";
			 i++;
		 }
		 console.log(text);
		 document.getElementById('category').innerHTML = text;
	 }
	 	 
	 jQuery(document).ready(function($) {//ロードされた時に処理を行う
		 var data = <?=json_encode($data)?>;
		 selectCategory(data);
		 $("input[type='radio']:eq(0)").prop('checked', true);
	 });
	 
	 //未入力項目のチェック
	 /* var flag = 0;
		function check () {
		if(document.getElementById('category').value == "") flag = 1;
		
		else if(document.form1.field2.value == "") flag = 1;
		else if(document.form1.field3.value == "") flag = 1;
		if(flag) {
		window.alert('必須項目に未入力がありました');
		return false;
		}
		else{
		return true;
		}
		}*/
	 
	</script>
	
</head>

<body>
	<h1>経費発生報告フォーム</h1>
	<?php echo $staff->name." : ".$staff->staffid; ?>
	<form id="statement" method="post" onSubmit="return check()" action="index.php">
		<h2>使用用途</h2>
		<span id="category"></span>
		<h2>精算先</h2>
		基本的につくば支部の精算です<br>
		<select name="bill_to">
			<option value="つくば">つくば</option>
			<option value="事務局">事務局</option>
			<option value="プロモ">プロモ</option>
			<option value="未来">未来</option>
		</select></br>
		<h2>名前</h2>
		<input type="text" name="user" value="<?=$staff->name?>"></br></br>
		<input type="submit" class="button button-border-primary button-rounded" value="詳細の入力">
	</form>
	<br>
	<a href="login.php">別のユーザでログイン</a><br>
	<a href="setting.php" class>設定</a></br>
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
