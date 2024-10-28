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

    try {
        // プロフィール画像のアップロード処理
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
            $fileType = $_FILES['profile_img']['type'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (in_array($fileType, $allowedTypes)) {
                // ユニークなファイル名を生成
                $extension = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid('profile_', true) . '.' . $extension;

                // ファイルをカレントディレクトリに保存
                move_uploaded_file($_FILES['profile_img']['tmp_name'], $imageName);

                // データベースに画像のファイル名を保存
                $updateSql = "UPDATE users SET profile = ?, profile_img = ?, is_anonymous = ? WHERE user_id = ?";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([$newProfile, $imageName, $isAnonymous, $userid]);
            } else {
                echo "無効な画像形式です。JPEG、PNG、GIF形式のファイルを選択してください。";
            }
        } else {
            // 画像がアップロードされていない場合は画像以外のデータのみ更新
            $updateSql = "UPDATE users SET profile = ?, is_anonymous = ? WHERE user_id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$newProfile, $isAnonymous, $userid]);
        }
        echo "プロフィールが更新されました。";
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
    <title>プロフィール設定画面</title>
    <link rel="stylesheet" href="css/usersettei.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="img/haikei4.png" alt="Full Screen Image">
        <div class="container">
            <a href="#" class="back-button" onclick="goBack()">←戻る</a>

            <!-- プロフィール画像表示と選択ボタン -->
            <div class="user-image" id="profile-image-preview" style="background-image: url('<?php echo htmlspecialchars($profile_img ?? 'default.png'); ?>');">
                <label for="image-upload" class="image-label">
                    画像を選択
                    <input type="file" id="image-upload" name="profile_img" style="display:none;" accept="image/*" onchange="previewImage(event)">
                </label>
            </div>

            <!-- ユーザープロフィール -->
            <form action="" method="POST" enctype="multipart/form-data">
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

        // 画像プレビューのスクリプト
        function previewImage(event) {
            const input = event.target;
            const reader = new FileReader();

            reader.onload = function() {
                const previewContainer = document.getElementById("profile-image-preview");
                previewContainer.style.backgroundImage = `url(${reader.result})`;
                previewContainer.style.backgroundSize = "cover";
                previewContainer.style.backgroundPosition = "center";
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
