<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>経費発生報告</title>
	</head>
	<body>
		
		<?php

		try {
			$pdo = new PDO('mysql:host=localhost;dbname=form_study;charset=utf8','root','qSJNFXBqw9Z5542D',
						   array(PDO::ATTR_EMULATE_PREPARES => false));
		} catch (PDOException $e) {
			exit('データベース接続失敗。'.$e->getMessage());
		}//接続

		$st = $pdo->prepare("INSERT INTO statement VALUES(?,?,?,?,?,?,?)");
		$st->execute(array($_POST['date'], $_POST['payee'],$_POST['content'],$_POST['business'],$_POST['detailed'],$_POST['account'],$_POST['money']));

		?>
			<p>登録が完了しました。<br /><a href="index.html">戻る</a></p>
	</body>
</html>
