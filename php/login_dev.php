<?php
session_start();

require "./dbConnect.php";

$mailaddress = $_POST['email'];
$password = $_POST['pass'];

function logincheck($email, $pass, $pdo){
    try {
        $sql = "SELECT * FROM users WHERE mailaddress = ?";
        
        $stmt = $pdo->prepare($sql); 
        
        // 値をバインド
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        
        // クエリ実行
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // パスワードを検証
        if  ($user['diagnosis_level'] == 0){

            $_SESSION['id'] = $user['user_id'];
            $_SESSION['email'] = $email;
            $_SESSION['pass'] = $pass;
            $_SESSION['diagnosis_level'] = $user['diagnosis_level'];

            header('Location:../leveldiagnosis.php');
            exit;
        }

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['email'] = $email;
            $_SESSION['pass'] = $pass;
            $_SESSION['diagnosis_level'] = $user['diagnosis_level'];

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
    header('Location:../home.php');
} else {
    header('Location:../login.php');
}
?>

