<?php
session_start();
require "./php/dbConnect.php";
// セッションにログイン情報がない場合はログイン画面にリダイレクト
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
// $post_id=1;
$post_id=$_GET['post_id'];
try {
    // postsテーブルとusersテーブルを結合して、post_detailとuser_name, profile_imgを取得
    $stmt = $pdo->prepare("
        SELECT posts.post_detail, posts.user_id,posts.post_flag, users.user_name, users.profile_img
        FROM posts
        INNER JOIN users ON posts.user_id = users.user_id
        WHERE posts.post_id = :post_id
    ");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // 結果を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
//匿名フラグが1の場合、ユーザー名を匿名にする
if ((int)$result['post_flag'] === 1) {
    $user_name = '匿名';
}else {
    $user_name = $result['user_name'];
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>コメント入力画面</title>
    <link rel="stylesheet" href="./css/commentnyuryoku.css?1">
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
    <div class="container">
        <a href="#" class="back-button" onclick="goBack()">←戻る</a>
        <div class="user-image"><span>画像</span></div>
        <!-- ユーザー名 -->
        <div class="user-name"> <?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?></div>
        <p id="post_detail">相談内容:<?php echo htmlspecialchars($result['post_detail'], ENT_QUOTES, 'UTF-8'); ?></p>
        <a href="#" class="tuduki">続きを読む</a>

        <form id="consultation-form" action="comment_check.php" method="POST">
            <textarea name="content" id="content" placeholder="コメント" required></textarea>

            <!-- 隠しフィールドでpost_detail, user_name, profile_img, post_idを送信 -->
            <input type="hidden" name="post_detail" value="<?php echo htmlspecialchars($result['post_detail']); ?>">
            <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>">
            <input type="hidden" name="profile_img" value="<?php echo htmlspecialchars($result['profile_img']); ?>">
            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
            <!-- 投稿者のuser_id -->
            <input type="hidden" name="post_user_id" value="<?php echo htmlspecialchars($result['user_id']); ?>">
            <button class="submit-button">確認する！</button>
        </form>
    </div>  
    </div>
    <script>
        // document.getElementById('consultation-form').addEventListener('submit', function(event) {
        //     event.preventDefault();
        //     var content = document.getElementById('content').value;
        //     var anonymous = document.getElementById('anonymous').checked;
        //     var postStyle = anonymous ? '匿名' : '公開';
        //     localStorage.setItem('postStyle', postStyle);
        //     localStorage.setItem('content', content);
        //     window.location.href = 'comment_check.php';
        // });

        function goBack() {
            location.href = "eturan.php";
        }
    </script>   
</body>
</html>