<?php
session_start();

require "./php/dbConnect.php"; // データベース接続


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $level = intval($_SESSION['diagnosis_level']);
    try {
        // メールアドレスでユーザー情報を取得
        $sql = "SELECT user_id, password FROM users WHERE mailaddress = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
             // パスワードが一致した場合
             $_SESSION['id'] = $user['id']; // セッションにuser_idを保存
             header('Location: home.php'); // ホーム画面にリダイレクト
             exit;
        } else {
            // パスワードが一致しない場合
            echo "メールアドレスまたはパスワードが間違っています。";
        }
    } catch (PDOException $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="./img/haikei4.png" alt="Full Screen Image">
        <div class="container">
        <div class="header">
            <h1>ログイン</h1>
            
        </div>
       
        <div class="login-form">
        
            <form action="./php/login_dev.php" method="post"> 
                

                <label for="email"><h3>メールアドレス</h3></label>
                <input type="email" id="email" name="email" required class="box"><br>
                <br>
   
                <label for="password"><h3>パスワード</h3></label>
                <input type="password" id="password" name="pass" requpasswoired class="box"><br>
                <br><br>

                <button type="submit">ログイン</button>
            </form>
            
        </div>
        <div class="footer">
            <p>アカウントをお持ちでない方はこちら</p>
            <a href="signup.php">新規登録</a><br>
        </div>
    </div>
    </div>

</body>
</html>
 