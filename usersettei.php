<?php
session_start();
require "./php/dbConnect.php"; // データベース接続

// セッションからユーザーIDを取得
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
            $profile_img = $user['profile_img'] ?? 'default.png';
            $profile = $user['profile'];
        } else {
            echo json_encode(['error' => 'ユーザー情報が見つかりませんでした。']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'エラーが発生しました: ' . $e->getMessage()]);
        exit;
    }
}

// 更新処理（Ajaxからのリクエストがあるとき）
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajax'])) {
    $newProfile = $_POST['profile'];
    $isAnonymous = isset($_POST['anonymous']) ? 1 : 0;
    $imageName = $_POST['uploaded_image'] ?? $profile_img;

    try {
        $updateSql = "UPDATE users SET profile = ?, profile_img = ?, is_anonymous = ? WHERE user_id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$newProfile, $imageName, $isAnonymous, $userid]);
        echo json_encode(['success' => true, 'image' => $imageName]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['error' => 'エラーが発生しました: ' . $e->getMessage()]);
        exit;
    }
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

            <div class="user-image" id="profile-image-preview" style="background-image: url('<?php echo htmlspecialchars("uploads/$profile_img"); ?>');">
                <label for="image-upload" class="image-label">
                    <span class="plus-icon">+</span>
                    <input type="file" id="image-upload" name="profile_img" style="display:none;" accept="image/*" onchange="uploadImage(event)">
                </label>
            </div>

            <form id="profile-form" method="POST">
                <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo htmlspecialchars($profile_img); ?>">
                <div class="username">
                    <label>ユーザープロフィール</label>
                    <textarea name="profile" class="user-profile"><?php echo htmlspecialchars($profile ?? ''); ?></textarea>
                </div>

                <div>
                    <label>
                        匿名で公開する 
                        <input type="checkbox" name="anonymous" <?php echo isset($isAnonymous) && $isAnonymous ? 'checked' : ''; ?>>
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
                    previewContainer.style.backgroundImage = `url(uploads/${data.image})`;
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
                    const previewContainer = document.getElementById("profile-image-preview");
                    previewContainer.style.backgroundImage = `url(uploads/${data.image})`;
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
