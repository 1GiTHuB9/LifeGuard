<?php
require './php/dbConnect.php';
session_start();

//if (!isset($_SESSION['id'])) {
    // ログインしていない場合はログインページにリダイレクト
  //  header('Location: login.php');
  //  exit();


// 初期値
$uploaded_image = 'img/user.png';
$profile = '';
$user_id = $_SESSION['id']; // セッションから user_id を取得

// データベースから現在のプロフィール情報を取得
try {
    $sql = "SELECT profile_img, profile FROM Users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $uploaded_image = $result['profile_img'] ?? $uploaded_image;
        $profile = $result['profile'] ?? $profile;
    }
} catch (PDOException $e) {
    echo 'エラー: ' . $e->getMessage();
}

// POSTリクエスト時に更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile = !empty($_POST['profile']) ? $_POST['profile'] : '';
    $uploadOk = 1; // アップロード成功フラグ
    $targetFile = $uploaded_image; // 初期値として現在の画像

    // 画像アップロード時に処理を実施
    if (!empty($_FILES['profile_img']['name'])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . uniqid() . "_" . basename($_FILES["profile_img"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 画像ファイルかどうか確認
        $check = getimagesize($_FILES["profile_img"]["tmp_name"]);
        if ($check === false) {
            $uploadOk = 0;
            echo "画像ファイルではありません。<br>";
        }

        // ファイルサイズの制限
        if ($_FILES["profile_img"]["size"] > 500000) { // 500KB以上はNG
            $uploadOk = 0;
            echo "ファイルサイズが大きすぎます。<br>";
        }

        // 画像ファイルの拡張子を確認
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $uploadOk = 0;
            echo "許可されていないファイル形式です。<br>";
        }

        // アップロード処理
        if ($uploadOk === 1) {
            if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $targetFile)) {
                $uploaded_image = $targetFile; // アップロード成功時に更新
            } else {
                echo "画像のアップロードに失敗しました。<br>";
            }
        }
    }

    // データベースに更新
    try {
        $sql = "UPDATE Users SET profile_img = :profile_img, profile = :profile WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':profile_img', $uploaded_image, PDO::PARAM_STR);
        $stmt->bindParam(':profile', $profile, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        echo "プロフィールが更新されました！<br>";
        header("Location: " . $_SERVER['PHP_SELF']); // 更新後、ページを再読み込み
        exit();
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

            <!-- プロフィール画像のプレビュー -->
            <div class="user-image" id="profile-image-preview" style="background-image: url('<?php echo htmlspecialchars($uploaded_image); ?>');">
                <label for="image-upload" class="image-label">
                    <span class="plus-icon">+</span>
                    <input type="file" id="image-upload" name="profile_img" style="display:none;" accept="image/*" onchange="previewAndUploadImage(event)">
                </label>
            </div>

            <form action="" method="POST" id="profile-form" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                

                <div class="username">
                    <label>ユーザープロフィール</label>
                    <textarea name="profile" class="user-profile"><?php echo htmlspecialchars($profile); ?></textarea>
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
    const previewContainer = document.getElementById("profile-image-preview");
    const uploadedImage = "<?php echo htmlspecialchars($uploaded_image); ?>";

    // 初期値としてアップロードされた画像がない場合、hiyoko.png を使用
    const imageUrl = uploadedImage && uploadedImage !== 'img/user.png' ? uploadedImage : 'img/user.png';
    previewContainer.style.backgroundImage = `url('${imageUrl}')`;
};


    </script>
</body>
</html>
