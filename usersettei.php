<?php
    require './php/dbConnect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_POST['user_id'];
        $profile = !empty($_POST['profile']) ? $_POST['profile'] : '';
        
        // デフォルト画像を設定
        $uploaded_image = 'img/shoki.png'; 

        if (!empty($_FILES['profile_img']['name'])) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . uniqid() . "_" . basename($_FILES["profile_img"]["name"]); // ユニークなファイル名
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // 画像ファイルかどうか確認
            $check = getimagesize($_FILES["profile_img"]["tmp_name"]);
            if ($check === false) {
                $uploadOk = 0;
            }

            // ファイルサイズの制限
            if ($_FILES["profile_img"]["size"] > 500000) { //500KB以上はNG
                $uploadOk = 0;
            }

            // 画像ファイルの拡張子を確認
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                $uploadOk = 0;
            }

            // アップロード処理
            if ($uploadOk === 1) {
                if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $targetFile)) {
                    $uploaded_image = basename($targetFile); // アップロードした画像のファイル名
                }
            }
        }

        try {
            // プロフィール画像とプロフィールテキストをデータベースに更新
            $sql = "UPDATE Users SET profile_img = :profile_img, profile = :profile WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':profile_img', $uploaded_image, PDO::PARAM_STR);
            $stmt->bindParam(':profile', $profile, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo 'エラー: ' . $e->getMessage();
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

            <div class="user-image" id="profile-image-preview" style="background-image: url('<?php echo htmlspecialchars($uploaded_image ?? 'img/shoki.png'); ?>');">
                <!-- プロフィール画像のプレビュー -->
                <label for="image-upload" class="image-label">
                    <span class="plus-icon">+</span>
                    <input type="file" id="image-upload" name="profile_img" style="display:none;" accept="image/*" onchange="previewAndUploadImage(event)">
                </label>
            </div>

            <form action="" method="POST" id="profile-form" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id ?? ''); ?>">
                <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo htmlspecialchars($uploaded_image ?? 'img/shoki.png'); ?>">

                <div class="username">
                    <label>ユーザープロフィール</label>
                    <textarea name="profile" class="user-profile"><?php echo htmlspecialchars($profile ?? ''); ?></textarea>
                </div>

                <div>
                    <button type="submit" class="update-button">更新する！</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function goBack() {
            history.back();
        }

        function previewAndUploadImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewContainer = document.getElementById("profile-image-preview");
                previewContainer.style.backgroundImage = `url(${e.target.result})`;
            }
            reader.readAsDataURL(file);
        }

        window.onload = function() {
            const uploadedImage = document.getElementById('uploaded_image').value;
            if (uploadedImage) {
                const previewContainer = document.getElementById("profile-image-preview");
                previewContainer.style.backgroundImage = `url(uploads/${uploadedImage})`;
            }
        };
    </script>
</body>
</html>
