<?php
session_start();

require "./php/dbConnect.php"; // データベース接続

if (isset($_SESSION['email']) && isset($_SESSION['pass'])) {

    $email = $_SESSION['email'];
    $password = $_SESSION['pass']; // ハッシュ化前のパスワード
    $level = intval($_SESSION['diagnosis_level']);

    if($level == 0){
        header('Location: ./leveldiagnosis.php');
        exit;
    }

    try {
        // ユーザーのメールアドレスに基づいてデータベースからハッシュ化されたパスワードを取得
        $sql = "SELECT password FROM users WHERE mailaddress = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // パスワードが一致すればホーム画面にリダイレクト
            header('Location: home.html');
            exit();
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
        



        <a href="leveldiagnosis.php">レベル診断</a>
        <a href="profilesetting.php">プロフィール</a><a href="diagnosisresult.php">診断結果</a>
    </div>
    </div>

</body>
</html>
 