<?php
session_start();

if(isset($_SESSION['id'])){
}else{
    header('Location: ./login.php');
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>相談フォーム</title>
    <link rel="stylesheet" href="./css/soudannyu.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
    <div class="container1">
        <a href="#" class="back-button" onclick="goBack()">←戻る</a>
        <h2>相談したいことを書き込もう！</h2>
        <form id="consultation-form">
            <textarea name="content" id="content" placeholder="相談内容" required></textarea>
            <div class="anonymous">
                <label for="anonymous">
                    <input type="checkbox" id="anonymous" name="anonymous">
                    匿名で投稿する
                </label>
            </div>
            <button type="submit" class="submit-button">確認する！</button>
        </form>
    </div>
    </div>
    <script>
        document.getElementById('consultation-form').addEventListener('submit', function(event) {
            event.preventDefault();
            var content = document.getElementById('content').value;
            var anonymous = document.getElementById('anonymous').checked;
            var postStyle = anonymous ? '匿名' : '公開';
            localStorage.setItem('postStyle', postStyle);
            localStorage.setItem('content', content);
            window.location.href = 'consul_check.php';
        });

        function goBack() {
            history.back();
        }
    </script>
</body>
</html>
