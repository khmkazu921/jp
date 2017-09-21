<?php
session_start();
require_once('functions.php');//未実装
$dbh = connectDB();

if(isset($_SESSION["username"])) {
	$status = "logged_in";
} else if(!empty($_SESSION["staff"]) && !empty($_SESSION["userid"])){
	$st = $dbh->prepare("SELECT  FROM staff WHERE username = ?");
	$st->bind_param('s', $_SESSION["username"]);
	$st->execute();
    $st->store_result();

	//結果の行数が1だったら成功
	if($st->num_rows == 1){
		$st->bind_result($pass);
		while ($st->fetch()) {
			if(password_verify($_POST["password"], $pass)){
        $status = "ok";
        //セッションにユーザ名を保存
        $_SESSION["username"] = $_POST["username"];
        break;
      }else{
        $status = "failed";
        break;
      }
    }
  }else
    $status = "failed";
}

?>

<!DOCTYPE html>
<html>o
  <head>
    <meta charset="UTF-8" />
    <title>ログイン</title>
  </head>
  <body>
    <?php if($status == "logged_in"): ?>
      <p>ログイン済み</p>
    <?php elseif($status == "ok"): ?>
      <p>ログイン成功</p>
    <?php elseif($status == "failed"): ?>
      <p>ログイン失敗</p>
    <?php else: ?>
      <form method="POST" action="login.php">
        ユーザ名：<input type="text" name="username" />
        パスワード：<input type="password" name="password" />
        <input type="submit" value="ログイン" />
      </form>
    <?php endif; ?>
  </body>
</html>
