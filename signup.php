<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <link rel="stylesheet" href="./css/style1.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="img/haikei4.png" alt="Full Screen Image">

        <a href="login.php" class="backlink">
                戻る
        </a>

        <div class="header">
            <h2>新規登録</h2><br>
            
        </div>
        <div class="signup-form">
        
            <form action="login.php" method="post"> 
            
                

                <label for="email">メールアドレス</label><br>
                <input type="email" id="email" name="email" required class="box"><br>
                <br>
   
                <label for="password">パスワード</label><br>
                <input type="password" id="password" name="password" required class="box"><br>
                <br><br>

                <button type="submit" class="Regist">登録</button>
            </form>
        </div>
    </div>
</body>
</html>