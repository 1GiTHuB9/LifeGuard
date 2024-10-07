<?php
session_start();

require "./dbConnect.php";

$mailaddress = $_POST['email'];
$password = $_POST['pass'];

function logincheck($email, $pass, $pdo){
    try {
        $sql = "SELECT mailaddress, password FROM users WHERE mailaddress = ?";
        
        $stmt = $pdo->prepare($sql); 
        
        // 値をバインド
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        
        // クエリ実行
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // パスワードを検証
        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['email'] = $email;
            $_SESSION['pass'] = $pass;

            return true;
        } else {
            return false; // パスワードが間違っている場合
        }
    } catch (PDOException $e) {
        // エラーメッセージをキャッチして表示
        echo "ログインに失敗しました。 " . $e->getMessage();
        return false;
    }
}


if(logincheck($mailaddress,$password,$pdo)){
    header('Location:../home.html');
} else {
    header('Location:../login.php');
}
?>

