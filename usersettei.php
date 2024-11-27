<?php
session_start();
require "./php/dbConnect.php"; // データベース接続

// 初期設定
$userid = $_SESSION['user_id'] ?? null;
$profile_img = $_SESSION['last_profile_img'] ?? null; // 前回のプロフィール画像をセッションから取得
$profile = '';
$isAnonymous = 0;

if ($userid) {
    try {
        // データベース接続確認
        if (!$pdo) {
            throw new PDOException("データベース接続に失敗しました。");
        }

        // ユーザープロフィール情報を取得
        $sql = "SELECT user_name, profile, profile_img, is_anonymous FROM users WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $profile_img = $user['profile_img'] ?? $profile_img; // データベースから取得（なければセッションの画像を使用）
            $profile = $user['profile'] ?? '';
            $isAnonymous = $user['is_anonymous'] ?? 0;
        }

        // プロフィール画像をセッションに保存
        $_SESSION['last_profile_img'] = $profile_img;
    } catch (PDOException $e) {
        // データベースエラー時、セッション画像を使用
        $error_message = "データベースエラー: " . htmlspecialchars($e->getMessage());
    }
}

// プロフィール画像パスを設定（キャッシュ防止のためにタイムスタンプを追加）
$profileImgPath = $profile_img ? "uploads/$profile_img?" . time() : '';
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

            <div class="user-image" id="profile-image-preview" style="background-image: url('<?php echo htmlspecialchars($profileImgPath); ?>');">
                <label for="image-upload" class="image-label">
                    <span class="plus-icon">+</span>
                    <input type="file" id="image-upload" name="profile_img" style="display:none;" accept="image/*" onchange="uploadImage(event)">
                </label>
            </div>

            <form id="profile-form" method="POST">
                <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo htmlspecialchars($profile_img); ?>">

                <div class="username">
                    <label>ユーザープロフィール</label>
                    <textarea name="profile" class="user-profile"><?php echo htmlspecialchars($profile); ?></textarea>
                </div>

                <div>
                    <label>
                        匿名で公開する 
                        <input type="checkbox" name="anonymous" <?php echo $isAnonymous ? 'checked' : ''; ?>>
                    </label>
                </div>

                <div>
                    <button type="button" class="update-button" onclick="updateProfile()">更新する！</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($error_message)): ?>
        <script>
            alert("<?php echo $error_message; ?>");
        </script>
    <?php endif; ?>

    <script>
        function goBack() {
            history.back();
        }

        // 画像のアップロード処理
        function uploadImage(event) {
            const file = event.target.files[0];
            const formData = new FormData();
            formData.append('profile_img', file);

            fetch('upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('uploaded_image').value = data.image;
                    const previewContainer = document.getElementById("profile-image-preview");
                    const timestamp = new Date().getTime(); // キャッシュ防止
                    previewContainer.style.backgroundImage = `url(uploads/${data.image}?${timestamp})`;
                } else {
                    alert(data.error || "画像のアップロードに失敗しました。");
                }
            })
            .catch(error => {
                console.error("エラー:", error);
            });
        }

        // プロフィール更新処理
        function updateProfile() {
            const formData = new FormData(document.getElementById('profile-form'));
            formData.append('ajax', true); // Ajax送信を示すパラメータ

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const timestamp = new Date().getTime(); // キャッシュ防止
                    document.getElementById("profile-image-preview").style.backgroundImage = `url(uploads/${data.image}?${timestamp})`;
                    alert("プロフィールが更新されました！");
                } else {
                    alert(data.error || "プロフィールの更新に失敗しました。");
                }
            })
            .catch(error => {
                console.error("エラー:", error);
            });
        }
    </script>
</body>
</html>
