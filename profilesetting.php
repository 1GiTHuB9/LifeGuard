<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール画面</title>
    <link rel="stylesheet" href="./css/style1.css">
    <style>
        .icon {
    width: 150px;
    height: 150px;
    border-radius: 50%; /* 丸くする */
    object-fit: cover; /* 画像を要素にフィットさせる */
    border: 2px solid #000000;
  }
    </style>
</head>
<body>
    <!-- <div class="fullscreen-image"> -->

        <!-- <img src="img/haikei4.png" alt="Full Screen Image"> -->
        
        <a href="#" class="back-button" onclick="goBack()">←戻る</a>

        <form action="profilesetting.php" class="setting-form">
            <img src="img/haikei4.png" alt="アイコン" class="icon">

            <h5>ここにプロフィールを表示</h5>

        </form>
    


      
    <!-- </div> -->
    <script>
        function goBack() {
            history.back();
        }
    </script>
</body>
</html>