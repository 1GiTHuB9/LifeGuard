<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール画面</title>
    <link rel="stylesheet" href="./css/style1.css">


</head>
<body>
    <div class="fullscreen-image">

        <img src="img/haikei4.png" alt="Full Screen Image">
    </div>
    <div class="container1">
        <a href="#" class="back-button" onclick="goBack()">←戻る</a>

        <form action="profilesetting.php" class="setting-form">
            <img src="img/haikei4.png" class="userimage">


            <div class="username">ユーザー名</div><br><br>
            <div class="user-profile">ユーザープロフィール</div>
            <textarea  class="userprofile">デフォルトの値
            </textarea><br>



            <button type="submit">更新</button>
        </form>
    </div>
        
    


      
    
    <script>
        function goBack() {
            history.back();
        }
    </script>
</body>
</html>