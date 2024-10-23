<?php
session_start();

require "./php/dbConnect.php"; // データベース接続

// セッションからユーザーIDを取得 (テスト用にIDを指定)
$_SESSION['user_id'] = 2201112;
$userid = $_SESSION['user_id'] ?? null;

if ($userid) {
    try {
        // ユーザー情報をデータベースから取得
        $sql = "SELECT user_name, profile_img, profile FROM users WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, $userid, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_name = $user['user_name'];
            $profile_img = $user['profile_img'];
            $profile = $user['profile'];
        } else {
            echo "ユーザー情報が見つかりませんでした。";
        }
    } catch (PDOException $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
}

// 更新処理（フォームが送信されたとき）
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newProfile = $_POST['profile'];
    $isAnonymous = isset($_POST['anonymous']) ? 1 : 0;
    
    // プロフィールと匿名設定を更新するSQL
    $updateSql = "UPDATE users SET profile = ?, is_anonymous = ? WHERE user_id = ?";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([$newProfile, $isAnonymous, $userid]);

    echo "プロフィールが更新されました。";
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール設定画面</title>
    <link rel="stylesheet" href="css/usersettei.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="img/haikei4.png" alt="Full Screen Image">
        <div class="container">
            <a href="#" class="back-button" onclick="goBack()">←戻る</a>
            
            <!-- プロフィール画像選択ボタン -->
            <div class="user-image">
                <label for="image-upload" class="image-label">
                    画像を選択
                    <input type="file" id="image-upload" name="profile_img" style="display:none;">
                </label>
            </div>

            <!-- ユーザープロフィール -->
            <form action="" method="POST">
                <div class="username">
                    <label>ユーザープロフィール</label>
                    <textarea name="profile" class="user-profile"><?php echo htmlspecialchars($profile ?? ''); ?></textarea>
                </div>

                <!-- 匿名で公開チェックボックス -->
                <div>
                    <label>
                        匿名で公開する 
                        <input type="checkbox" name="anonymous" <?php echo isset($isAnonymous) && $isAnonymous ? 'checked' : ''; ?>>
                    </label>
                </div>

                <!-- 更新ボタン -->
                <div>
                    <button type="submit" class="update-button">更新する！</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- 戻るボタンのスクリプト -->
    <script>
        function goBack() {
            history.back();
        }
    </script>
</body>
</html>
