<!DOCTYPE html>
<html lang="ja">

<?php
session_start();
header('Content-type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');
require_once('functions.php');		

//ログイン検証
login_confirmation();

$staff = $_POST['staff'];
$bill_to = $_POST['bill_to'];

//listだけcategoryによって絞る
$dbh = connectDb();
$sql = "";
if(isset($_POST['category']))
	$sql = "SELECT * FROM list WHERE category='" . $_POST['category'] . "'";
$st = $dbh->query($sql);
$data['list'] = $st->fetchAll(PDO::FETCH_ASSOC);

//その他は普通に取得
$d_table = array("category", "place", "payee", "destination");
foreach($d_table as $val) {
	$st = $dbh->query("SELECT * FROM " . $val);
	$data[$val] = $st->fetchAll(PDO::FETCH_ASSOC);
}//データベース取得

header('Content-Type: application/json');
?>

<head>
	<meta charset="UTF-8">
	<title>りっぴーくん</title>
	<script type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	<link rel="stylesheet" href="css/buttons.css">
	
	<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
	<script type="text/javascript" src="js/buttons.js"></script>
	<script>
	 var account = ["交通費", "通信費"];
	 var business_detailed = ["集客", "説明会"];
	 var business = ["全体", "未来"];
	 var bill_to = ["つくば", "事務局"];
	 
	 var content_check = false;
	 var payee_check = false;
	 var destination_check = false;
	 
	 function selectFilter(element, id) {
		 if (element[this.name] == this.id) return true;
		 return false;
	 }
	 
	 //payeeのオプションを表示
	 function payeeOption(data, category) {
		 //カテゴリーに当てはまるものを抽出
		 var text;
		 var id = {'name':'category', 'id': category}; //カテゴリーのvalue
		 
		 //支払先の表示
		 var payee_f = data.payee.filter(selectFilter, id);
		 for(var val of payee_f) {
			 text += "<option value=\"" + val.name + "\">" + val.name + "</option>";
		 }
		 document.getElementById('payee').innerHTML = text;
	 }
	 
	 //contentのオプションを表示
	 function contentOption(data, category) {
		 //カテゴリーに当てはまるものを抽出
		 //var id = {'name':'category', 'id': category}; //カテゴリーのvalue
		 var list_f = data.list;//.filter(selectFilter, id);		 
		 //内容の表示
		 var text = "<select name='name' id='content-select'>";
		 for(var val of list_f) {
			 text += "<option value='" + val.name + "'>" + val.name +  "</option>";
		 }
		 text += "</select>";
		 document.getElementById('content').innerHTML = text;
		 return list_f[0].name;
	 }
	 
	 //destinationのオプションを表示
	 function destinationOption(data, category) {
		 console.log(data.destination);
		 var dest = data.destination;
		 //行き先の表示
		 var text = "<select name='destination' id='destination-select'>";
		 for(var val of dest) {
			 text += "<option value='" + val.name + "'>" + val.name +  "</option>";
		 }
		 text += "</select>";
		 document.getElementById('destination').innerHTML = text;
		 return dest[0].name;
	 }
	 
	 //placeのオプションの表示
	 function placeOption(data, category) {
		 //カテゴリーに当てはまるものを抽出
		 var id = {'name':'category', 'id': category};
		 var place_f = data.place.filter(selectFilter, id);
		 
		 //場所の表示
		 var text = "<select name='place' id='place-select'>";
		 for(var val of place_f) {
			 text += "<option value='" + val.name + "'>" + val.name +  "</option>";
		 }
		 text += "</select>";
		 document.getElementById('place').innerHTML = text;
		 return place_f[0].name;
	 }
	 
	 //自動入力される値を渡す部分
	 function setHidden(list, cat_id, content) {
		 var li = {'name':'id', 'id': cat_id};
		 var list_f = list.filter(selectFilter, li);
		 
		 if(business[cat_id] != null && account[cat_id] != null && business_detailed[cat_id] != null) {
			 var behind_text = "<input type='hidden' value='";
			 var behind_text2 = "<input type='hidden' name='";
			 var hidden = behind_text + business[cat_id] + "' name='business'>";
			 hidden += behind_text + account[cat_id] + "' name='account'>";
			 hidden += behind_text + business_detailed[cat_id] + "' name='business_detailed'>";
			 hidden += behind_text + content + "' name='name'>";
			 hidden += behind_text2 + "staff' value='<?=$staff?>'>";
			 hidden += behind_text2 + "bill_to' value='<?=$bill_to?>'>";
			 hidden += behind_text2 + "item' value='statement'></br>";
			 document.getElementById('hidden').innerHTML = hidden;
		 } else {
			 console.log("ERROR");
		 }
	 }
	 
	 //内容をテキストボックスで入力
	 function tBoxContent() {
		 var data = <?=json_encode($data)?>;
		 content_check = true;
		 var con_val = document.getElementById('content-select').value;
		 var pla_val = document.getElementById('place-select').value;
		 var des_val = document.getElementById('destination-select').value;
		 text = shapingContentNameA(con_val, pla_val, des_val, data, <?=$_POST['category']?>);
		 var out = "<input type='text' name='name' id='textbox-content' value='" + text + "'></br>";
		 document.getElementById('out-of-textbox-content').innerHTML = out;
	 }

	 function tBoxSheets() {
		 var out = "<input type='number' name='sheets' id='textbox-sheets'></br>";
		 document.getElementById('out-of-textbox-sheets').innerHTML = out;
	 }
	 
	 //支払先をテキストボックスで入力
	 function tBoxPayee() {
		 payee_check = true;
		 var text = "<input type='text' name='payee' id='textbox-payee' value='";
		 text += document.getElementById('payee').value + "'>";
		 document.getElementById('textbox-payee').innerHTML = text;
	 }

	 function tBoxDestination() {
		 destination_check = true;
		 var text = "<input type='text' id='textbox-destination' value='";
		 text += document.getElementById('destination-select').value + "'>";
		 document.getElementById('out-of-textbox-destination').innerHTML = text;
	 }
	 
	 /**
	  *  @param cat_id カテゴリーのID（phpから） 
	  *　@param data jsonデータ
	  */
	 
	 //交通費、会場費名前の整形
	 function shapingContentNameA(content, place, destination, data, disp_id) {
		 var cat_name = "";
		 var con_name = "";
		 //カテゴリーの名前の抽出/交通費
		 switch (disp_id) {
			 case 1:
				 cat_name = "交通費";
				 var con_name = content + "@" + place + " " + cat_name;
				 break;
			 case 2:
				 cat_name = "の会場代";
				 con_name = content + "@" + place + cat_name;
				 break;
			 default:
				 break;
		 }
		 return con_name;
	 }

	 //その他の項目名の整形
	 function shapingContentNameB(content, data, disp_id) {
		 
	 }
	 
	 function displayCategory(data, cat_id) {
		 document.getElementById('category').innerHTML =
			 data.category.filter(
				 function(item) { if(item.id == cat_id) return true; }
			 )[0].name;
	 }
	 
	 function dispId() {
		 var disp_id;
		 if(this.match(/交通費/)) {
			 disp_id = 0;
		 } else if(this.match(/会場/)) {
			 disp_id = 1;
		 } else if(this.match(/枚数/)) {
			 disp_id = 2;
		 } else {
			 disp_id = 3;
		 }
		 return disp_id;
	 }

	 
		
	 //ロードされた時に処理を行う
	 jQuery(document).ready(function($) {
		 
		 //データをjsonに登録
		 var data = <?=json_encode($data)?>;
		 var cat_id = <?=$_POST['category']?>;
		 
		 displayCategory(data, cat_id);
		 //支払先のオプション表示
		 payeeOption(data, cat_id);
		 //場所のオプション表示
		 var pla_first = placeOption(data, cat_id);
		 //内容のオプション表示
		 var con_first = contentOption(data, cat_id);
		 //行き先のオプション表示
		 var des_first = destinationOption(data, cat_id);


		 var disp = document.getElementById('content-select').value;
		 console.log(disp);
		 var disp_id = disp.dispId();
		 //注意
		 var content;
		 switch (disp_id) {
			 case 0:
				 content = shapingContentNameA(con_first, pla_first, data, disp_id) + "\r\n" + des_first;
			 case 1:
				 content = shapingContentNameA(con_first, pla_first, data, disp_id);
			 case 2:
				 content = shapingContentNameB(con_first, data);
			 default:
				 content = con_first;
		 }		 

		 setHidden(data.list, cat_id, content);

		 //支払先を触るとtextBoxの中身を変更する
		 $("#payee").change(function() {
			 console.log(payee_check);
			 if(payee_check) {
				 var con_val = document.getElementById('content-select').value;
				 var pla_val = document.getElementById('place-select').value;
				 var des_val = document.getElementById('payee').value;
				 content = shapingContentName(con_val, pla_val, data, cat_id) + "\r\n" + des_val;
				 tBoxPayee();
				 setHidden(data.list, cat_id, content);//ここでcontentの値を渡す
			 }			 
		 });

		 //内容を触ると表示画面が変更される
		 //内容に交通費会場代が含まれている時、disp_id==0：placeを表示
		 //内容に枚数などが含まれている時、disp_id==1：枚数を表示
		 //その他はdisp_id：表示しない
		 $(document).on('change', '#content-select', function(){
			 console.log("asdf");
			 var disp_id = this.value.dispId();
			 switch (disp_id) {
				 case 0:
					 if(content_check) {
						 document.getElementById('out-of-textbox-sheets').innerHTML = "";
						 var pla_val = document.getElementById('place-select').value;
						 var des_val = document.getElementById('destination-select').value;
						 content = shapingContentNameA(this.value, pla_val, data, cat_id) + "\r\n" + des_val;		 
						 tBoxContent();
					 }
					 break;
				 case 1:
					 if(content_check) {
						 document.getElementById('out-of-textbox-sheets').innerHTML = "";
						 var pla_val = document.getElementById('place-select').value;
						 var des_val = document.getElementById('destination-select').value;
						 content = shapingContentNameA(this.value, pla_val, data, cat_id);		 
						 tBoxContent();
					 }
					 break;
				 case 2:
					 content = this.value;
					 tBoxSheets();
					 break;
				 default:
					 
					 //document.getElementById('place').innerHTML = "";
			 }
			 setHidden(data.list, cat_id, content);//ここでcontentの値を渡す
		 });
		 
		 //場所を触るとtextBoxの中身を変更する
		 $("#place-select").change(function() {
			 if(content_check) {
				 var con_val = document.getElementById('content-select').value;
				 var des_val = document.getElementById('destination-select').value;
				 console.log(this.value);
				 content = shapingContentNameA(con_val, this.value, data, cat_id) + "\r\n" + des_val;
				 tBoxContent();
				 setHidden(data.list, cat_id, content);//ここでcontentの値を渡す
			 }
		 });

		 //行き先を触るとtextBoxの中身を変更する
		 $("#destination-select").change(function() {
			 if(destination_check) {
				 var con_val = document.getElementById('content-select').value;
				 var pla_val = document.getElementById('place-select').value;
				 console.log(this.value);
				 content = shapingContentNameA(con_val, pla_val, data, cat_id) + "\r\n" + this.value;
				 tBoxDestination();
				 setHidden(data.list, cat_id, content);//ここでcontentの値を渡す
			 }
		 });
		 
		 //内容のテキストボックスを変更するとhiddenが変更される
		 $(document).on('keyup', '#textbox-content', function(){
			 var con_val = this.value;
			 var pla_val = document.getElementById('place-select').value;
			 var des_val = document.getElementById('destination-select').value;
			 content = shapingContentNameA(con_val, pla_val, data, cat_id) + "\r\n" + des_val;
			 setHidden(data.list, cat_id, content);
		 });

		 $(document).on('keyup', '#textbox-destination', function(){
			 console.log("あいうえお");
			 var con_val = document.getElementById('content-select').value;
			 var pla_val = document.getElementById('place-select').value;
			 var des_val = this.value;
			 var content = shapingContentNameA(con_val, pla_val, data, cat_id) + "\r\n" + des_val;
			 setHidden(data.list, cat_id, content);
		 });
	 });
	 
	 function numOnly() {
		m = String.fromCharCode(event.keyCode);
		if("0123456789\b\r".indexOf(m, 0) < 0) return false;
		return true;
	 }
	 
	</script>
</head>
<body>
	
	<h1>経費発生報告：入力型</h1>

	<form id="statement" method="post" action="insert.php">
		カテゴリー</br>
		<span id="category"></span></br>

		日付</br>
		<input type="date" name="date"></br>

		支払先</br>
		<select name="payee" id="payee"></select></br>
		<span id="textbox-payee"></span>
		<input type="button" class="button button-border-primary button-rounded" value="変更" onClick="tBoxPayee()"></br>

		内容/場所</br>
		<span id="content"></span>@<span id="place"></span></br>
		<span id="out-of-textbox-content"></span>
		<input type="button" class="button button-border-primary button-rounded" value="変更" onClick="tBoxContent()">
		<span id="out-of-textbox-sheets"></span></br>

		行き先</br>
		<span id="destination"></span>
		<span id="out-of-textbox-destination"></span>
		<input type="button" class="button button-border-primary button-rounded" value="変更" onClick="tBoxDestination()"></br>
		
		金額</br>
		<input type="number" id="cost" name="cost" onkeyDown="return numOnly()">
		<span id="hidden"></span>
		<p>
			<input type="submit" value="追加" class="button button-border-primary button-rounded">
		</p>
		
	</form>
	<p></p>
	<br>
	<a href="start.php">戻る</a></br>
	<a href="setting.php">設定</a>
	
</body>
</html>
