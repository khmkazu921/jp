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
			<title>経費発生報告フォーム</title>
			<script type="text/javascript"></script>
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
			<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
			<link rel="stylesheet" href="css/buttons.css">
			
			<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
			<script type="text/javascript" src="js/buttons.js"></script>
			<script>
			 //ここをデータとしてセットする
			 var account = ["交通費", "通信費", "消耗品費", "図書費", "印刷費", "会議費", "レンタル費", "会費", "適用不明"];
			 var business_detailed = ["MTG", "支部維持その他", "集客", "説明会", "集客イベント", "選考会", "二次選考", "学生集客その他", "継続訪問", "新規訪問", "訪問その他", "実地フォロー", "初回イベント", "中間イベント", "最終イベント", "学生フォローその他", "全社業務", "その他"];
			 var business = ["全体","インターン","グローバル","未来"];
			 var bill_to = ["つくば", "事務局", "未来", "プロモ"];
			 var content_check = false;
			 var place_check = false;
			 var payee_check = false;
			 var destination_check = false;

			 var cat_id = <?=$_POST['category']?>;
			 
			 function selectFilter(element, id) {
				 if (element[this.name] == this.id) return true;
				 return false;
			 }

			 function getContentId() {
				 var tmp = document.getElementById('content-select');
				 if(tmp == null) {
					 return 1;
				 }
				 return tmp.value;
			 }
			 
			 function selectContentArray(data) {
				 var id = getContentId();
				 var li = {'name':'id', 'id': id};
				 var list = data.filter(selectFilter, li)[0];
				 return list;
			 }
			 
			 //category
			 function displayCategory(data, cat_id) {
				 document.getElementById('category').innerHTML =
					 data.category.filter(
						 function(item) { if(item.id == cat_id) return true; }
					 )[0].name;
			 }
			 
			 //payee
			 //payeeのオプションを表示
			 function dispPayeeOption(data, category) {
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

			 //payeeボタンの設置
			 function dispPayeeButton() {
				 var text = "<input type='button' class='button button-border-primary button-rounded' value='変更' onClick='dispPayeeTextbox()'></br>";
				 document.getElementById('payee-button').innerHTML = text;
			 }			 
			 
			 //payeeを非表示
			 function disappPayee() {
				 document.getElementById('payee').innerHTML = "";
			 }
			 
			 //payeeをテキストボックスで入力
			 function dispPayeeTextbox() {
				 payee_check = true;
				 var text = "<input type='text' name='payee' id='textbox-payee' value='";
				 text += document.getElementById('payee').value + "'><br>";
				 document.getElementById('textbox-payee').innerHTML = text;
			 }

			 //payeeのテキストボックスから取得
			 function getPayeeFromTextBox() {
				 var tmp = document.getElementById('content-select');
				 if(tmp == null) {
					 return "";
				 }
				 return tmp.value;
			 }				 

			 //content
			 
			 //contentのオプションを表示
			 function dispContentOption(data, category) {
				 //カテゴリーに当てはまるものを抽出
				 var list_f = data.list;
				 
				 //内容の表示
				 var text = "<select name='name' id='content-select'>";
				 for(var val of list_f) {
					 text += "<option value='" + val.id + "'>" + val.display +  "</option>";
				 }
				 text += "</select><br>";
				 document.getElementById('content').innerHTML = text;
				 return list_f[0].name;
			 }

			 //contentボタンの設置
			 function dispContentButton() {
				 var text = "<input type='button' class='button button-border-primary button-rounded' value='変更' onClick='dispContentTextbox()'><br><br>";
				 document.getElementById('content-button').innerHTML = text;
			 }

			 //contentボタンの削除
			 function disappContentButton() {
				 document.getElementById('content-button').innerHTML = "";
			 }
			 
			 //contentのオプションからの取得
			 function getContentFromSelect(data) {
				 var list = selectContentArray(data.list);
				 return list.name;
			 }

			 //contentのタイトル表示
			 function dispContentTitle() {
				 document.getElementById('content-title').innerHTML = "内容<br>";
			 }

			 function dissapContentTitle() {
				 document.getElementById('content-title').innerHTML = "";
			 }
			 
			 //contentをテキストボックスで入力
			 function dispContentTextbox() {
				 var data = <?=json_encode($data)?>;
				 content_check = true;
				 var con_val = getContentFromSelect(data);
				 var out = "<input type='text' name='name' id='textbox-content' value='" + con_val + "'></br>";
				 document.getElementById('out-of-textbox-content').innerHTML = out;
			 }

			 //contentのテキストボックスからの取得
			 function getContentFromTextbox() {
				 return document.getElementById('textbox-content').value;
			 }

			 //destination

			 //destinationボタンの設置
			 function dispDestinationButton() {
				 var text = "<input type='button' class='button button-border-primary button-rounded' value='変更' onClick='dispDestinationTextbox()'></br>";
				 document.getElementById('destination-button').innerHTML = text;
			 }

			 function disappDestinationButton() {
				 document.getElementById('destination-button').innerHTML = "";
			 }

			 function dispDestinationTitle() {
				 document.getElementById('destination-title').innerHTML = "行き先<br>";
			 }

			 function disappDestinationTitle() {
				 document.getElementById('destination-title').innerHTML = "";
			 }
			 
			 //destinationのオプションを表示
			 function dispDestinationOption(data, category) {
				 var dest = data.destination;
				 //行き先の表示
				 var text = "<select name='destination' id='destination-select'>";
				 for(var val of dest) {
					 text += "<option value='" + val.name + "'>" + val.name +  "</option>";
				 }
				 text += "</select></br>";
				 document.getElementById('destination').innerHTML = text;
				 return dest[0].name;
			 }
			 
			 //destinationのオプションから取得
			 function getDestinationFromSelect() {
				 var tmp = document.getElementById('destination-select');
				 if(tmp == null) {
					 return "";
				 }
				 return tmp.value;
			 }
			 
			 //destinationを非表示
			 function disappDestination() {
				 document.getElementById('destination').innerHTML = "";
			 }

			 //destinationをテキストボックスで入力
			 function dispDestinationTextbox() {
				 destination_check = true;
				 var des_val = getDestinationFromSelect();
				 var out = "<input type='text' name='name' id='textbox-destination' value='" + des_val + "'></br>";
				 document.getElementById('out-of-textbox-destination').innerHTML = out;
			 }

			 //destinationのテキストボックスからの取得
			 function getDestinationFromTextBox() {
				 var tmp = document.getElementById('textbox-destination');
				 if(tmp == null){
					 return "";
				 }
				 return tmp.value;
			 }

			 //place

			 //placeボタンの設置
			 function dispPlaceButton() {
				 var text = "<input type='button' class='button button-border-primary button-rounded' value='変更' onClick='dispPlaceTextbox()'></br><br>";
				 document.getElementById('place-button').innerHTML = text;
			 }

			 function disappPlaceButton() {
				 document.getElementById('place-button').innerHTML = "";
			 }

			 //placeボタンの削除
			 function dissapPlaceButton() {
				 document.getElementById('place-button').innerHTML = "";
			 }
			 
			 //placeのオプションの表示
			 function dispPlaceOption(data, category) {
				 //カテゴリーに当てはまるものを抽出
				 var id = {'name':'category', 'id': category};
				 var place_f = data.place.filter(selectFilter, id);
				 
				 //場所の表示
				 var text = "@<select name='place' id='place-select'>";
				 for(var val of place_f) {
					 text += "<option value='" + val.name + "'>" + val.name +  "</option>";
				 }
				 
				 text += "</select><br>";
				 document.getElementById('place').innerHTML = text;
				 if(place_f[0] == null) {
					 return "";
				 } else {
					 return place_f[0].name;
				 }
			 }
			 
			 //placeのタイトルの設置
			 function dispPlaceTitle() {
				 document.getElementById('place-title').innerHTML = "場所<br>";
			 }
			 
			 function disappPlaceTitle() {
				 document.getElementById('place-title').innerHTML = "";
			 }

			 //placeのテキストボックス削除
			 function disappPlace() {
				 document.getElementById('place').innerHTML = "";
				 document.getElementById('out-of-textbox-place').innerHTML = "";
			 }

			 //placeのテキストボックス表示
			 function dispPlaceTextbox() {
				 place_check = true;
				 var pla_val = getPlaceFromSelect();
				 var out = "<input type='text' name='name' id='textbox-place' value='" + pla_val + "'></br>";
				 document.getElementById('out-of-textbox-place').innerHTML = out;
			 }

			 //placeのセレクトボックスから値を受け取る
			 function getPlaceFromSelect() {
				 var tmp = document.getElementById('place-select');
				 if(tmp == null) {
					 return "";
				 }
				 return tmp.value;
			 }

			 //placeのテキストボックスから値を受け取る
			 function getPlaceFromTextbox() {
				 var tmp = document.getElementById('textbox-place');
				 if(tmp == null) {
					 return "";
				 }
				 return tmp.value;
			 }
			 
			 //sheets
			 
			 //sheetsの表示
			 function dispSheetsTextbox() {
				 var out = "<br><input type='text' value='1' name='sheets' id='textbox-sheets'></br>";
				 document.getElementById('out-of-textbox-sheets').innerHTML = out;
			 }

			 function dispCostTitle() {
				 document.getElementById('cost-title').innerHTML = "金額";
			 }

			 function disappCostTitle() {
				 document.getElementById('cost-title').innerHTML = "";
			 }

			 //sheetsの取得
			 function getSheets() {
				 var tmp = document.getElementById('textbox-sheets');
				 if(tmp == null) {
					 return "";
				 }
				 console.log(tmp.value);
				 return tmp.value;
			 }

			 //sheetsの非表示
			 function disappSheets() {
				 document.getElementById('out-of-textbox-sheets').innerHTML = "";
			 }

			 function dispSheetsTitle() {
				 document.getElementById('sheets-title').innerHTML = "枚数";
			 }

			 function disappSheetsTitle() {
				 document.getElementById('sheets-title').innerHTML = "";
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
					 case 0:
						 if(cat_id == 6){
							 cat_name = "交通費(高校生)";
						 } else {
							 cat_name = "交通費";
						 }
						 con_name = content + "@" + place + " " + cat_name + "\r\n(" + destination + ")";
						 break;
					 case 1:
						 if(cat_id == 6){
							 cat_name = "の会場代(高校生)";
						 } else {
							 cat_name = "の会場代";
						 }
						 con_name = content + "@" + place + cat_name;
						 break;
					 case 3:
						 if(cat_id == 6){
							 cat_name = "燃料費(高校生)";
						 } else {
							 cat_name = "燃料費";
						 }
						 con_name = content + "@" + place + " " + cat_name + "\r\n(" + destination + ")";
						 break;
					 case 4:
						 if(cat_id == 6){
							 cat_name = "駐車場代(高校生)";
						 } else {
							 cat_name = "駐車場代";
						 }
						 con_name = content + "@" + place + " " + cat_name;
						 break;
					 case 5:
						 if(cat_id == 6){
							 cat_name = "高速代(高校生)";
						 } else {
							 cat_name = "高速代";
						 }
						 con_name = content + "@" + place + " " + cat_name + "\r\n(" + destination + ")";
						 break;
					 case 6:
						 if(cat_id == 6){	 
							 cat_name = "レンタカー代(高校生)";
						 } else {
							 cat_name = "レンタカー代";
						 }
						 con_name = content + "@" + place + " " + cat_name + "\r\n(" + destination + ")";
						 break;
					 default:
						 if(cat_id == 6){
							 con_name = content + "(高校生)";
						 } else {
							 con_name = content;
						 }
						 break;
				 }
				 return con_name;
			 }
			 
			 //枚数入力型項目の整形
			 function shapingContentNameB(content, sheets) {
				 var cat_name = "の用紙代（";
				 var con_name = content + cat_name + sheets +"枚）";
				 return con_name;
			 }

			 //自動入力される値を渡す部分
			 function setHidden(list, content) {
				 if(business[list.business] != null && account[list.account] != null && business_detailed[list.business_detailed] != null) {
					 var behind_text = "<input type='hidden' value='";
					 var behind_text2 = "<input type='hidden' name='";
					 var hidden = behind_text + business[list.bussiness] + "' name='business'>";
					 hidden += behind_text + account[list.account] + "' name='account'>";
					 hidden += behind_text + business_detailed[list.business_detailed] + "' name='business_detailed'>";
					 hidden += behind_text + content + "' name='name'>";
					 hidden += behind_text2 + "staff' value='<?=$staff?>'>";
					 hidden += behind_text2 + "bill_to' value='<?=$bill_to?>'>";
					 hidden += behind_text2 + "item' value='statement'></br></br>";
					 var display = content + " " + business[list.business] + " " + business_detailed[list.business_detailed] + " " + account[list.account] + "</br></br>";
					 document.getElementById('hidden').innerHTML = hidden;
					 document.getElementById('display').innerHTML = display;
				 } else {
					 console.log("ERROR");
				 }
			 }
			 
			 //表示方法の選別関数
			 function dispId(disp) {
				 var disp_id;
				 if(~disp.indexOf('')) disp_id = 4;
				 if(~disp.indexOf('運賃')) disp_id = 0;
				 else if(~disp.indexOf('会場費')) disp_id = 1;
				 else if(~disp.indexOf('用紙代')) disp_id = 2;
				 else if(~disp.indexOf('切手')) disp_id = 2;
				 else if(~disp.indexOf('ガソリン代')) disp_id = 3;
				 else if(~disp.indexOf('駐車場代')) disp_id = 4;
				 else if(~disp.indexOf('高速料金')) disp_id = 5;
				 else if(~disp.indexOf('レンタカー代')) disp_id = 6;
				 else disp_id = 7;
				 return disp_id;
			 }

			 //内容を触ると表示画面が変更される
			 //内容に運賃が含まれている時
			 //  disp_id==0 3 5 6：place、destinationを表示、sheetsを削除
			 //内容に会場代が含まれている時
			 //  disp_id==1：placeを表示、destination、sheetsを削除
			 //内容に枚数などが含まれている時
			 //  disp_id==2：sheetsを表示、place、destinationを削除
			 //その他はdisp_id：表示しない
			 function first(data) {
				 var list = selectContentArray(data.list);
				 var disp_id = dispId(list.display);
				 var con_val = getContentFromSelect(data);
				 switch (disp_id) {
					 case 0:
					 case 3:
					 case 4:
					 case 5:
					 case 6:
						 dispPlaceTitle();
						 dispPlaceOption(data, cat_id);
						 dispPlaceButton();
						 dispDestinationTitle();
						 dispDestinationOption(data, cat_id);
						 dispDestinationButton();
						 disappSheetsTitle();
						 disappSheets();

						 //変更ボタンを押していたらテキストボックスも表示
						 if(content_check) {
							 dispContentTextbox();
						 }//よくわからない
						 if(place_check) {
							 dispPlaceTextbox();
							 var pla_val = getPlaceFromTextbox();
						 } else {
							 var pla_val = getPlaceFromSelect();
						 }
						 if(destination_check) {
							 dispDestinationTextbox();
							 var des_val = getDestinationFromTextBox();
						 } else {
							 var des_val = getDestinationFromSelect();
						 }
						 content = shapingContentNameA(con_val, pla_val, des_val, data, disp_id);
						 break;

					 case 1:
						 dispPlaceTitle();
						 dispPlaceOption(data, cat_id);
						 dispPlaceButton();
						 disappDestinationTitle();
						 disappDestination();
						 disappDestinationButton();
						 disappSheetsTitle();
						 disappSheets();
						 
						 if(content_check) {
							 dispContentTextbox();
						 }
						 if(place_check) {
							 dispPlaceTextbox();
							 var pla_val = getPlaceFromTextbox();
						 } else {
							 var pla_val = getPlaceFromSelect();
						 }
						 content = shapingContentNameA(con_val, pla_val,  "", data, 1);
						 break;
						 
					 case 2:
						 disappPlaceTitle();
						 disappPlace();
						 disappPlaceButton()
						 disappDestinationTitle();
						 disappDestination();
						 disappDestinationButton();
						 dispSheetsTitle();
						 dispSheetsTextbox();
						 
						 var sheets = getSheets();
						 if(content_check) {
							 dispContentTextbox();
							 con_val = getContentFromTextbox();
						 } else {
							 con_val = getContentFromSelect(data);
						 }
						 content = shapingContentNameB(con_val, sheets);
						 break;

					 default:
						 disappPlace();
						 disappPlaceButton();
						 disappDestination();
						 disappDestinationButton();
						 disappSheets();
						 disappSheetsTitle();
						 disappDestinationTitle();
						 disappPlaceTitle();
						 
						 if(content_check) {
							 dispContentTextbox();
						 }
						 content = getContentFromSelect(data);
				 }

				 //ここでcontentの値を渡す
				 var list = selectContentArray(data.list);
				 setHidden(list, content);
			 }
			 
			 //ロードされた時に処理を行う
			 jQuery(document).ready(function($) {
				 
				 //データをjsonに登録
				 var data = <?=json_encode($data)?>;
				 
				 displayCategory(data, cat_id);
				 //支払先のオプション表示
				 dispPayeeOption(data, cat_id);

				 dispContentButton();
				 dispDestinationButton();
				 dispDestinationTitle();
				 dispPlaceButton();
				 dispPlaceTitle();
				 dispPayeeButton();
				 dispCostTitle();

				 //場所のオプション表示
				 var pla_first = dispPlaceOption(data, cat_id);
				 //内容のオプション表示
				 var con_first = dispContentOption(data, cat_id);
				 var con_val = getContentFromSelect(data);
				 //行き先のオプション表示
				 var des_first = dispDestinationOption(data, cat_id);

				 var disp = getContentFromSelect(data);
				 var disp_id = dispId(disp);
				 //注意
				 var content;
				 switch (disp_id) {
					 case 0:
					 case 1:
					 case 3:
					 case 4:
					 case 5:
					 case 6:
						 content = shapingContentNameA(con_val, pla_first, des_first, data, disp_id);
						 break;
					 case 2:
						 var sheets = getSheets();
						 content = shapingContentNameB(con_val, sheets);
						 break;
					 default:
						 content = con_val;
				 }

				 //ここで絞り込んだリストを渡す
				 var list = selectContentArray(data.list);
				 setHidden(list, content);//ここでcontentの値を渡す

				 //支払先を触るとtextBoxの中身を変更する
				 $("#payee").change(function() {
					 console.log(payee_check);
					 if(payee_check) {
						 dispPayeeTextbox();
					 }			 
				 });

				 //内容を触ると中身を変更する
				 first(data);				 
				 $(document).on('change', '#content-select', function(){
					 first(data);
				 });

				 //場所を触るとtextBoxの中身を変更する
				 $(document).on('change', '#place-select', function(){
					 var con_val = getContentFromSelect(data);
					 var des_val = getDestinationFromSelect();
					 var pla_val = getPlaceFromSelect();
					 var disp_id = dispId(con_val);
					 content = shapingContentNameA(con_val, pla_val, des_val, data, disp_id);
					 var list = selectContentArray(data.list);
					 setHidden(list, content);//ここでcontentの値を渡す
					 if(place_check) {
						 dispPlaceTextbox();
					 }
				 });

				 //行き先を触るとtextBoxの中身を変更する
				 $(document).on('change', '#destination-select', function(){
					 if(destination_check) {
						 var con_val = getContentFromSelect(data);
						 var pla_val = getPlaceFromSelect();
						 var disp_id = dispId(con_val);
						 content = shapingContentNameA(con_val, pla_val, this.value, data, disp_id);
						 dispDestinationTextbox();
						 var list = selectContentArray(data.list);
						 setHidden(list, content);//ここでcontentの値を渡す
					 }
				 });

				 //sheetsが変更された時にhiddenが変更させる
				 $(document).on('keyup', '#textbox-sheets', function(){
					 var con_val = getContentFromSelect(data);
					 var she_val = getSheets();
					 content = shapingContentNameB(con_val, she_val);
					 var list = selectContentArray(data.list);
					 setHidden(list, content);
				 });
				 
				 //内容のテキストボックスを変更するとhiddenが変更される
				 $(document).on('keyup', '#textbox-content', function(){
					 var con_val = this.value;
					 var pla_val = getPlaceFromSelect();
					 var des_val = getDestinationFromSelect();
					 getContentFromSelect(data);
					 var disp_id = dispId(selectContentArray(data.list).display);
					 var sheets = getSheets();
					 var content;
					 switch (disp_id) {
						 case 0:
						 case 1:
						 case 3:
						 case 4:
						 case 5:
						 case 6:
							 content = shapingContentNameA(con_val, pla_val, des_val, data, disp_id);
							 break;
						 case 2:
							 content = shapingContentNameB(con_val, sheets);
							 break;
						 default:
							 content = con_val;
							 break;
					 }
					 var list = selectContentArray(data.list);
					 setHidden(list, content);//ここでcontentの値を渡す
				 });

				 //場所のテキストボックスを変更するとhiddenが変更される
				 $(document).on('keyup', '#textbox-place', function(){
					 var con_val = getContentFromSelect(data);
					 var pla_val = this.value;
					 var des_val = getDestinationFromSelect();
					 var disp_id = dispId(selectContentArray(data.list).display);
					 content = shapingContentNameA(con_val, pla_val, des_val, data, disp_id);
					 var list = selectContentArray(data.list);
					 setHidden(list, content);//ここでcontentの値を渡す
				 });
				 
				 //行き先のテキストボックスを変更するとhiddenが変更される
				 $(document).on('keyup', '#textbox-destination', function(){
					 var con_val = getContentFromSelect(data);
					 var pla_val = getPlaceFromSelect();
					 var des_val = this.value;
					 var disp_id = dispId(selectContentArray(data.list).display);
					 var content = shapingContentNameA(con_val, pla_val, des_val, data, disp_id);
					 var list = selectContentArray(data.list);
					 setHidden(list, content);//ここでcontentの値を渡す
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
				<input type="date" name="date" value="<?echo date('Y-m-');?>"></br>

				支払先</br>
				<select name="payee" id="payee"></select></br>
				<span id="textbox-payee"></span>
				<span id="payee-button"></span><br>

				内容</br>
				<span id="content"></span>
				<span id="out-of-textbox-content"></span>
				<span id="content-button"></span>

				<span id="place-title"></span>
				<span id="place"></span>
				<span id="out-of-textbox-place"></span>
				<span id="place-button"></span>

				<span id="destination-title"></span>
				<span id="destination"></span>
				<span id="out-of-textbox-destination"></span>
				<span id="destination-button"></span>

				<span id="sheets-title"></span>
				<span id="out-of-textbox-sheets"></span>

				<span id="display"></span>
				
				<span id="cost-title"></span></br>

				<input type="number" id="cost" name="cost" onkeyDown="return numOnly()">円
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
