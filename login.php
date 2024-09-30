<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン画面</title>
    <link rel="stylesheet" href="./css/style1.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="./img/haikei4.png" alt="Full Screen Image">

        <div class="header">
            <h1>ログイン</h1>
            
        </div>
       
        <div class="login-form">
        
            <form action="home.php" method="post"> 
                

                <label for="email"><h3>メールアドレス</h3></label>
                <input type="email" id="email" name="email" required class="box"><br>
                <br>
   
                <label for="password"><h3>パスワード</h3></label>
                <input type="password" id="password" name="rd" requpasswoired class="box"><br>
                <br><br>

                <button type="submit">ログイン</button>
            </form>
            
        </div>
        <div class="footer">
            <p>アカウントをお持ちでない方はこちら</p>
            <a href="signup.php">新規登録</a><br>
        </div>
        



        <a href="signup.php">新規登録</a><a href="leveldiagnosis.php">レベル診断</a>
        <a href="profilesetting.php">プロフィール</a><a href="diagnosisresult.php">診断結果</a>
    </div>

</body>
</html>
 