<?php 
session_start();
require "./php/dbConnect.php"; // データベース接続

// セッションからユーザーIDを取得
$userid = $_SESSION['user_id'] ?? null;

// 初期値の設定
$defaultProfileImg = 'img/default_profile.png'; // デフォルトのプロフィール画像
$profile_img = null;
$profile = '';
$isAnonymous = 0;

if ($userid) {
    try {
        // ユーザープロフィール情報を取得
        $sql = "SELECT u.user_name, u.profile, ui.file_name AS profile_img, u.is_anonymous
                FROM users u
                LEFT JOIN uploaded_images ui ON u.user_id = ui.user_id
                WHERE u.user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userid]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // 前回のデータが存在する場合は設定
            $profile_img = $user['profile_img'] ?? null;
            $profile = $user['profile'] ?? '';
            $isAnonymous = $user['is_anonymous'] ?? 0;
        }
    } catch (PDOException $e) {
        die("エラー: " . $e->getMessage());
    }
}


// プロフィール画像のパスを設定（キャッシュ防止用のタイムスタンプ付き）
$profileImgPath = $profile_img ? "uploads/$profile_img?" . time() : $defaultProfileImg;
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
