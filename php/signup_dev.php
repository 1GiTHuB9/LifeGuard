<?php
session_start();
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
    $sql = "INSERT INTO users(mailaddress, password,user_name) VALUES (?, ?,?)";
    $stmt = $pdo->prepare($sql); 

    // 値をバインド
    $stmt->bindValue(1, $_POST['email'], PDO::PARAM_STR);
    $stmt->bindValue(2, $hashedPassword, PDO::PARAM_STR);
    $stmt->bindValue(3, $_POST['name'], PDO::PARAM_STR);

    // クエリ実行
    if($stmt->execute()){
        //新規登録ユーザのIDを取得し、セッションに保存
        $_SESSION['id'] = $pdo->lastInsertId();//最後に挿入されたユーザーIDを取得

        echo "<script type='text/javascript'>alert('アカウント登録が完了しました'); window.location.href = '../leveldiagnosis.php';</script>";
        exit();
    }else{
        //実行に失敗した場合
        echo "<script type='text/javascript'>alert('登録に失敗しました。再度お試しください。'); window.location.href = '../signup.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    // エラーメッセージをキャッチして表示
    echo "データの挿入中にエラーが発生しました: " . $e->getMessage();
}
?>