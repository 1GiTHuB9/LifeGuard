<?php
session_start();

require "./php/dbConnect.php"; // データベース接続
//テスト用
$_SESSION['user_id']=2201112;
if (isset($_SESSION['user_id'])) {

    $userid = $_SESSION['user_id'];

    try {
        // user_idに基づいて、user_name, profile_img, profileをデータベースから取得
        $sql = "SELECT user_name, profile_img, profile FROM users WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $userid, PDO::PARAM_INT);  // セッションのuser_idを利用
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // データベースから取得した情報をセッションに保存
            $user_name = $user['user_name'];
            $profile_img = $user['profile_img'];
            $profile = $user['profile'];

            // header('Location: otheruser.html');
            // exit();
        }
    } catch (PDOException $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>相手ユーザー情報表示画面</title>
    <link rel="stylesheet" href="./css/otheruser.css">
</head>

<body>
    <div class="fullscreen-image">
        <img src="img/haikei4.png" alt="Full Screen Image">
    <div class="container">
        <a href="#" class="back-button" onclick="goBack()">←戻る</a>

<!-- プロフィール表示エリア -->
<section class="user-info">
    <div class="user-image">
        <!-- プロフィール画像を表示 -->
        <?php if (isset($profile_img)): ?>
            <img src="userimg/<?php echo htmlspecialchars($profile_img); ?>.jpg" alt="プロフィール画像">
        <?php else: ?>
            <label for="profile_img" class="image-label">
                画像を選択
                <input type="file" id="profile_img" name="profile_img" style="display:none;">
            </label>
        <?php endif; ?>
    </div>

    <div class="username">
        <!-- ユーザー名を表示 -->
        <?php if (isset($user_name)): ?>
            <h2><?php echo htmlspecialchars($user_name); ?></h2>
        <?php else: ?>
            <h2>ユーザープロフィール</h2>
        <?php endif; ?>
    </div>
</section>
    </body>
</html>
