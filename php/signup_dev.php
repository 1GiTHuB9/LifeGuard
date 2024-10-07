<?php
require "./dbConnect.php";

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
    header('Location:../login.php');
    exit();
} catch (PDOException $e) {
    // エラーメッセージをキャッチして表示
    echo "データの挿入中にエラーが発生しました: " . $e->getMessage();
    header('Location:../signup.php');
}
?>