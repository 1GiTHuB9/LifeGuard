<?php
session_start();

require "./php/db.php"; // データベース接続


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $email = $_SESSION['email'];
    $password = $_SESSION['pass']; // ハッシュ化前のパスワード
    $level = intval($_SESSION['dlevel']);

    
    if($level == 0){
        header('Location: ./leveldiagnosis.php');
        exit;
    }

    try {
        // メールアドレスでユーザー情報を取得
        $sql = "SELECT user_id, password, diagnosis_level FROM users WHERE mailaddress = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            // ログイン成功時
            $_SESSION['id'] = $user['user_id'];
            $_SESSION['dlevel'] = $user['dlevel']; // 診断レベルを保存
    
            if ($_SESSION['dlevel'] === 0) {
                header('Location: ./leveldiagnosis.php');
            } else {
                header('Location: home.php');
            }
            exit;
        } else {
            // パスワードが一致しない場合
            $_SESSION['error'] = "メールアドレスまたはパスワードが間違っています。";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "エラーが発生しました: " . $e->getMessage();
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
                
                <?php
                // エラーメッセージが設定されていない場合のみ <br> を表示
                if(!isset($_SESSION['error']) && !isset($_SESSION['e'])){
                    echo "<br><br>";
                }
                ?>
                <span id="errorCheck" class="error-message">
                <?php
                if(isset($_SESSION['error'])){
                    echo "<p>" . $_SESSION['error'] . "</p>";
                    unset($_SESSION['error']);
                  } else if(isset($_SESSION['e'])){
                    echo "<p>" . $_SESSION['e'] . "</p>";
                    unset($_SESSION['e']);
                  } 
                ?>
                </span>  
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
 