<?php
session_start();

require "./php/dbConnect.php"; // データベース接続

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
            $_profile_img = $user['profile_img'];
            $_profile = $user['profile'];

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

<!-- ユーザー情報表示エリア -->        
<section class="user-info">
    <div class="user-image">
        <!-- プロフィール画像を表示 -->
        <?php if (isset($_SESSION['profile_img'])): ?>
            <img src="<?php echo htmlspecialchars($_SESSION['profile_img']); ?>" alt="プロフィール画像">
        <?php else: ?>
            <span>画像がありません</span>
        <?php endif; ?>
    </div>

    <div class="username">
        <!-- ユーザー名を表示 -->
        <?php if (isset($_SESSION['user_name'])): ?>
            <h2><?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
        <?php else: ?>
            <h2>ユーザー名がありません</h2>
        <?php endif; ?>
    </div>
</section>

<!-- プロフィール表示エリア -->
<section class="user-profile">
    <header><h3>プロフィール</h3></header>
    <!-- プロフィール内容を表示 -->
    <?php if (isset($_SESSION['profile'])): ?>
        <p><?php echo nl2br(htmlspecialchars($_SESSION['profile'])); ?></p>
    <?php else: ?>
        <p>プロフィールがありません</p>
    <?php endif; ?>
</section>

<!-- 相談内容表示エリア -->
<section class="consultation-content">
    <header><h3>相談内容</h3></header>
    <p>******************************************************</p>
</section>

    <script>
        function goBack() {
            history.back();
        }
    </script>
    </body>
</html>