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
                    <span class="plus-icon">+</span>
                    <input type="file" id="image-upload" name="profile_img" style="display:none;" accept="image/*" onchange="previewAndUploadImage(event)">
                </label>
            </div>
 
            <!-- ユーザープロフィール -->
            <form action="upload.php" method="POST" id="profile-form" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo htmlspecialchars($profile_img ?? ''); ?>">
 
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
 
            const formData = new FormData();
            formData.append('profile_img', file);
 
            fetch('upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 新しい画像のファイル名をhidden inputに設定
                    document.getElementById('uploaded_image').value = data.image;
                } else {
                    alert(data.error || "画像のアップロードに失敗しました。");
                }
            })
            .catch(error => {
                console.error("エラー:", error);
            });
        }
 
        window.onload = function() {
            const uploadedImage = document.getElementById('uploaded_image').value;
            if (uploadedImage) {
                const previewContainer = document.getElementById("profile-image-preview");
                previewContainer.style.backgroundImage = `url(uploads/${uploadedImage})`;
            }
        }
    </script>
</body>
</html>
