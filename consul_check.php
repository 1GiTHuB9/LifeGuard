
<?php 
session_start();
if(isset($_SESSION['id'])){
    // データベースからユーザー情報を取得
    require "./php/dbConnect.php";
    $sql = "SELECT profile_img FROM users WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}else{
    header('Location: ./login.php');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>相談確認画面</title>
    <link rel="stylesheet" href="./css/soudankaku.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
        <div class="overlay"></div>
        <div class="container1">
            <a href="#" class="back-button" onclick="goBack()">←戻る</a>
            <div class="user-info">
                <div class="user-image">
                <?php
                // 画像のパスが存在すれば表示、無ければデフォルトの画像を表示
                if (!empty($user['profile_img'])) {
                    echo "<img src='./{$user['profile_img']}' alt='Profile Image' class='profile-image'>";
                } else {
                echo "<img src='./img/user.png' alt='Default Profile Image' class='profile-image'>";
                }
                ?>
                </div>
                <div class="user-name"><?php echo $_SESSION['uname']; ?></div>
            </div>
            <div class="post-style">投稿スタイル：<span id="post-style"></span></div>
            <div class="consultation-content">
                相談内容
                <p id="content-display"></p>
            </div>

            <form id="submit-form" method="POST" action="./php/soudannyuryoku_dev.php" style="display:none;">
                <input type="hidden" name="postStyle" id="postStyleInput">
                <input type="hidden" name="content" id="contentInput">
            </form>
            
            <button class="submit-button">投稿する！</button>

        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //ローカルストレージからデータを取得
            var postStyle = localStorage.getItem('postStyle');
            var content = localStorage.getItem('content');

            // HTMLに表示
            document.getElementById('post-style').textContent = postStyle;
            document.getElementById('content-display').textContent = content;

            // フォームのinput要素に値をセット
            document.getElementById('postStyleInput').value = postStyle;
            document.getElementById('contentInput').value = content;
        });
        // 投稿ボタンのクリック処理
        document.querySelector('.submit-button').addEventListener('click', function() {
             document.getElementById('submit-form').submit();
        });
        function goBack() {
            history.back();
        }
    </script>
</body>
</html>
