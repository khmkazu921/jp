<!DOCTYPE html>
<html lang="ja">
	<meta charset="UTF-8">
	<title>りっぴーくん</title>
	<body>
		
		<form action="setting_table.php" method="post">
			<select name="item">
			<option value="list">一覧</option>
			<option value="place">場所</option>
			<option value="category">カテゴリー</option>
			<option value="payee">支払先</option>
			<option value="staff">スタッフ</option>
			<option value="statement">精算書</option>

			</select></br>

		<input type="submit" value="選択">
		<input type="reset" value="リセット">
		</form>
		<a href="start.php">経費発生入力フォーム</a>
	</body>
</html>
