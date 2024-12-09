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
        <!-- ボタンのタイプを submit に変更 -->
        <button type="submit" class="update-button">更新する！</button>
    </div>
</form>

<script>
    document.getElementById("profile-form").addEventListener("submit", function (event) {
        event.preventDefault(); // デフォルトのフォーム送信をキャンセル

        const formData = new FormData(this);
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
    });
</script>
