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
	
	$st1 = $dbh->query("SELECT * FROM category");
	$cat = $st1->fetchAll(PDO::FETCH_ASSOC);
	$st2 = $dbh->query("SELECT * FROM list");
	$lis = $st2->fetchAll(PDO::FETCH_ASSOC);
	$st3 = $dbh->query("SELECT * FROM place");
	$pla = $st3->fetchAll(PDO::FETCH_ASSOC);
	
	// JSON形式で出力する
	header('Content-Type: application/json');
	?>
	
	<script>
	 var cat = <?php echo json_encode( $cat , JSON_UNESCAPED_UNICODE );?>;
	 var lis = <?php echo json_encode( $lis , JSON_UNESCAPED_UNICODE );?>;
	 var pla = <?php echo json_encode( $pla , JSON_UNESCAPED_UNICODE );?>;
	 
	 /*
		document.write("[");
		for(var i = 0; i < lis.length; i++) {
		document.write("{");
		for(key in lis[i]) {
		document.write(key + ":" + lis[i][key] + ",");
		}
		document.write("},");
		}
		document.write("]</br>");
	  */	
 	
	 jQuery(document).ready(function($) {//ロードされた時に処理を行う

		 for(var i in cat) {
			 $("#category").append("<option value=" + cat[i].id + ">" + cat[i].name +  "</option>");
		 }

		 var text = "";		 
		 for(var i in lis) {
			 text += "<option value=" + lis[i].id + ">" + lis[i].name +  "</option>";
		 }
		 console.log(text);
		 $("#contents").append(text);
		 
		 $('#category').change(function() {

			 var id = $(this).val();
			 var place_id = {'name':'category','index':id };
			 $('p').text(id);
			 
			 function selectFilter(element, index) {
				 if (element[this.name] == this.index) return true;
				 return false;	
			 }
			 
			 var lis_f = lis.filter(selectFilter, place_id);
			 var pla_f = pla.filter(selectFilter, place_id);
			 var text = "";		 
			 for(var i in lis_f) {
				 text += "<option value=" + lis_f[i].id + ">" + lis_f[i].name +  "</option>";
			 }
			 for(var i in pla_f) {
				 text += "<option value=" + lis_f[i].id + ">" + lis_f[i].name +  "</option>";
			 }
			 for(var i in lis_f) {
				 text += "<option value=" + lis_f[i].id + ">" + lis_f[i].name +  "</option>";
			 }
			 
			 $("#contents").text("");
			 $("#contents").text(text);
			 
		 });
	 });
	 
	</script>
	<?php echo json_encode( $cat , JSON_UNESCAPED_UNICODE );?></br>
	<?php echo json_encode( $lis , JSON_UNESCAPED_UNICODE );?></br>
	<?php echo json_encode( $pla , JSON_UNESCAPED_UNICODE );?></br>
	
	<form name="statement" method="post" action="set_statement.php">
		<select id="category"></select></br>
		<select id="contents"></select></br>
		<select id="place"></select></br>
		<select id="cdselection"></select></br>
		<input type="text" name="cost" style="text-align: right; "/></input></br>
		<input type="submit" value="追加">
	</form>
	<p></p>
	<br>
	<a href="http://localhost:8888/index.html">戻る</a>
	<br>
	<a href="http://localhost:8888/statement/index.html">入力型</a>
	<br>
	<a href="http://localhost:8888/statement/index2.php">選択型</a>
		</br>
		<a href="http://localhost:8888/statement/setting.php">設定</a>
		
	</body>
</html>
