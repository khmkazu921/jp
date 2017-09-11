<!DOCTYPE html>
<html lang="ja">
	<meta charset="UTF-8">
	<title>りっぴーくん</title>
	<body>
		
		<form action="setting_table.php" method="post">
		<p>	
			<input type="radio" name="item" value="account"/>勘定科目</br>
			<input type="radio" name="item" value="business"/>事業</br>
			<input type="radio" name="item" value="category"/>カテゴリー</br>
			<input type="radio" name="item" value="content_detailed"/>内容詳細</br>
			<input type="radio" name="item" value="detailed"/>業務内容</br>
			<input type="radio" name="item" value="place"/>場所</br>
			<input type="radio" name="item" value="list"/>一覧</br>
			<input type="radio" name="item" value="staff"/>スタッフ</br>
			<input type="radio" name="item" value="statement">精算書</br>
			<input type="radio" name="item" value="payee">支払先</br>

			</select></p>
<p>
		<input type="submit" value="選択">
		<input type="reset" value="リセット"></p>
		</form>
		<a href="index.php">経費発生入力フォーム</a>
	</body>
</html>
