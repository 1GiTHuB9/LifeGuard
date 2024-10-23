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

        //メールアドレス検証
        if(isset($user)){
        } else {
            $error = "メールアドレスまたはパスワードが間違っています。";
            return $error;
            exit;
        }

        // パスワードを検証
        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['email'] = $email;
            $_SESSION['pass'] = $pass;
            $_SESSION['uname'] = $user['user_name'];
            $_SESSION['pimg'] = $user['profile_img'];
            $_SESSION['diagnosis_level'] = $user['diagnosis_level'];

            if  ($user['diagnosis_level'] == 0){
                header('Location:../leveldiagnosis.php');
                exit;
            }

            $success = "ログインに成功しました。";
            return $success;
        } else {
            $error = "メールアドレスまたはパスワードが間違っています。";
            return $error;
        }
    } catch (PDOException $e) {
        // エラーメッセージをキャッチして表示
        echo "ログインに失敗しました。 " . $e->getMessage();
        return $e;
    }
}

$result = logincheck($mailaddress,$password,$pdo);
$success = "ログインに成功しました。";
$error = "メールアドレスまたはパスワードが間違っています。";

if($result == $success){
    header('Location:../home.html');
} else if($result == $error){
    $_SESSION['error'] = $error;
    header('Location:../login.php');
} else {
    $_SESSION['e'] = $e;
    header('Location:../login.php');
}
?>

