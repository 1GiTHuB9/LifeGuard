<?php
session_start();
// ログインしているユーザーのIDを取得
$user_id = $_SESSION['id'];
require "./php/dbConnect.php";
try {
    // ログインしているユーザーの user_name と profile_img を取得
    $stmt = $pdo->prepare("
        SELECT user_name, profile_img 
        FROM users 
        WHERE user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $login_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // データが取得できたか確認
    if ($login_user) {
        $login_user_name = $login_user['user_name'];
        $login_profile_img = $login_user['profile_img'];
    } else {
        echo "<p>ログインユーザー情報が見つかりません。</p>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
// データがPOSTされているか確認
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $anonymous = isset($_POST['anonymous']) ? '匿名' : '公開';
    $post_detail = $_POST['post_detail'];
    $user_name = $_POST['user_name'];
    $profile_img = $_POST['profile_img'];
    $post_id = $_POST['post_id'];

} else {
    echo "<p>データが送信されていません。</p>";
}
?>
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
        <!-- ログインユーザー名 -->
        <div class="user-name"><?php echo htmlspecialchars($login_user_name); ?></div>
        <p>投稿スタイル：<span id="post-style"><?php echo htmlspecialchars($anonymous); ?></span></p>
        <p>投稿内容:<span id="content-display"><?php echo htmlspecialchars($content); ?></span></p>
        <p>投稿先</p>
        <div class="user-image2"><span>画像</span></div>
        <!-- 投稿先ユーザー名 -->
        <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
        <!-- 投稿先相談内容 -->
        <p><?php echo htmlspecialchars($post_detail); ?></p>
        <!-- コメント挿入用のフォーム -->
        <form action="./php/comment_insert.php" method="POST">
            <input type="hidden" name="comment_detail" value="<?php echo htmlspecialchars($content); ?>">
            <input type="hidden" name="comment_flag" value="<?php echo $anonymous; ?>">
            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <button type="submit" class="submit-button">投稿する！</button>
        </form>
        <!-- <button class="submit-button">投稿する！</button> -->
    </div>
    </div> 
    <script>
    // document.addEventListener('DOMContentLoaded', function() {
    //         var postStyle = localStorage.getItem('postStyle');
    //         var content = localStorage.getItem('content');
    //         document.getElementById('post-style').textContent = postStyle;
    //         document.getElementById('content-display').textContent = content;
    //     });

    function goBack() {
        history.back();
    }    
    </script>
</body>
</html>