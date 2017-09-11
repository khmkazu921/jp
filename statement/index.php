<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>りっぴーくん2</title>
		<script type="text/javascript">
		 
		</script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

	</head>
	<!--	<body onLoad="changeSelection()"> -->
	<h1>経費発生報告：入力型</h1>
	
	<?php
	
	header('Content-type: text/plain; charset=UTF-8');
	header('Content-Transfer-Encoding: binary');
	require_once('functions.php');		
	$dbh = connectDb();

	$table = array("claim_to","category", "list", "place", "content_detailed", "payee");
	//array_splice
	foreach($table as $val) {
		$st = $dbh->query("SELECT * FROM " . $val);
		$data[$val] = $st->fetchAll(PDO::FETCH_ASSOC);
	}
	
		//ここからもう一度。今日中にユーザ指定から出力まで。名前、日付！<自動ソート
		//その他を選んだ時、任意入力できる。精算先を入力する
	header('Content-Type: application/json');
	?>
	
	<script>
	 function selectFilter(element, id) {
		 if (element[this.name] == this.id) return true;
		 return false;	
	 }
	 
	 jQuery(document).ready(function($) {//ロードされた時に処理を行う
		 var data = <?=json_encode($data)?>;
		 //<p></p>にhtmlの出力
		 for(var key in data) {
			 var text = key + "<br><select id=" + key + ">";			 
			 for(var val of data[key])
				 text += "<option value=" + val.id + ">" + val.name +  "</option>";
			 $("#statement").append(text + "</select></br>");
		 }
		 
		 //カテゴリー変更されたら選択肢を変更
		 $('#category').change(function() {	 
			 var id = {'name':'category', 'id': $(this).val() /*<カテゴリーのvalue*/};
			 delete data.category;
			 delete data.claim_to;
			 for(var key in data) {
				 var data_f = data[key].filter(selectFilter, id);
			 	 text = "";
				 for(var val of data_f)
					 text += "<option value=" + val.id + ">" + val.name +  "</option>";
				 // $("#" + key).text(text); 何故か動かないので▼
				 document.getElementById(key).innerHTML = text;
			 }
		 });
	 });
	 
	</script>
	
	<form method="post" action="set_statement.php">
		<p id="statement"></p>
		<input type="text" name="cost" style="text-align: right; "/></input></br>
		<input type="submit" value="追加">
	</form>
	<br>
	<a href="index.html">戻る</a>
	<br>
		<a href="setting.php">設定</a>
		
	</body>
</html>
