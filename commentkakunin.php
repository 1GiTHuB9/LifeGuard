<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コメント確認画面</title>
    <link rel="stylesheet" href="./css/commentkakunin.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
    <div class="container">
        <a href="#" class="back-button" onclick="goBack()">←戻る</a>
        <div class="user-image"><span>画像</span></div>
        <div class="user-name"> ユーザー名</div>
        <p>投稿スタイル：<span id="post-style"></span></p>
        <p>投稿内容:<span id="content-display"></span></p>
        <p>投稿先</p>
        <div class="user-image2"><span>画像</span></div>
        <div class="user-name"> ユーザー名</div>
        
        <p>相談内容＊＊＊＊＊＊＊＊＊＊＊</p>
        <button class="submit-button">投稿する！</button>
    </div>
    </div> 
    <script>
    document.addEventListener('DOMContentLoaded', function() {
            var postStyle = localStorage.getItem('postStyle');
            var content = localStorage.getItem('content');
            document.getElementById('post-style').textContent = postStyle;
            document.getElementById('content-display').textContent = content;
        });

    function goBack() {
        history.back();
    }    
    </script>
</body>
</html>