<!DOCTYPE html>
<html lang="ja">

<?php
session_start();
header('Content-type: text/plain; charset=UTF-8');
header('Content-Transfer-Encoding: binary');
require_once('functions.php');
//http://webtre.hatenablog.jp/entry/2015/06/14/［PHP］php.iniファイルの初期設定

$staff = $_SESSION['staff'];
echo $staff;
$dbh = connectDb();
$st = $dbh->query("SELECT * FROM category");
$data = $st->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');


// google
// アプリケーション設定
define('CONSUMER_KEY', '709072939097-hbun9dp9cjuq9q45bsoqrverfhp49esk.apps.googleusercontent.com');
define('CONSUMER_SECRET', 'u3-EXdB_660nU5HYxCYRoaB9');
define('CALLBACK_URL', 'http://localhost:8888/statement/start.php');

// URL
define('TOKEN_URL', 'https://accounts.google.com/o/oauth2/token');
define('INFO_URL', 'https://www.googleapis.com/oauth2/v1/userinfo');


//--------------------------------------
// アクセストークンの取得
//--------------------------------------
$params = array(
	'code' => $_GET['code'],
	'grant_type' => 'authorization_code',
	'redirect_uri' => CALLBACK_URL,
	'client_id' => CONSUMER_KEY,
	'client_secret' => CONSUMER_SECRET,
);

// POST送信
$options = array('http' => array(
	'method' => 'POST',
	'content' => http_build_query($params)
));
$res = file_get_contents(TOKEN_URL, false, stream_context_create($options));

// レスポンス取得
$token = json_decode($res, true);
if(isset($token['error'])){
	echo 'エラー発生';
	exit;
}
$access_token = $token['access_token'];
$_SESSION['token'] = $access_token;

//--------------------------------------
// ユーザー情報を取得してみる
//--------------------------------------
$params = array('access_token' => $access_token);
$res = file_get_contents(INFO_URL . '?' . http_build_query($params));
echo "<pre>" . print_r(json_decode($res, true), true) . "</pre>";
?>
	
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

<?php
echo $_SESSION['staff'];
?>
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
	<input type="text" name="staff" value="<?=$staff?>"></br>
	<input type="submit" value="スタート">
</form>
<p></p>
<br>
<a href="google.php">ログイン</a></br>
<a href="setting.php">設定</a></br>
<a href="output.php">出力</a>
	</body>
</html>
