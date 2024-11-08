<?php
require "./dbConnect.php";

function getParam($key, $pattern, $error){
	//POSTのパラメータを取り出す
	$val = filter_input(INPUT_POST, $key);

	//文字エンコーディング(UTF-8)のチェック
	if(! mb_check_encoding($val, 'UTF-8')){
		die('文字エンコーディングが不正です');
	}

	//引数で受け取ったパターンでチェック
	if(preg_match($pattern, $val) !== 1) {
		die($error);
	}
	return $val;
}

try {

    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // SQL文を準備
    $sql = "INSERT INTO users(mailaddress, password) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql); 
    
    // 値をバインド
    $stmt->bindValue(1, $_POST['email'], PDO::PARAM_STR);
    $stmt->bindValue(2, $hashedPassword, PDO::PARAM_STR);
    
    // クエリ実行
    $stmt->execute();
    $alert = "<script type='text/javascript'>alert('アカウント登録が完了しました');</script>";
    echo $alert;
    header('Location: ../leveldiagnosis.php');
    exit();
} catch (PDOException $e) {
    // エラーメッセージをキャッチして表示
    echo "データの挿入中にエラーが発生しました: " . $e->getMessage();
    header('Location:../signup.php');
}
?>