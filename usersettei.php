<?php
require './php/dbConnect.php';
session_start();

//if (!isset($_SESSION['id'])) {
    // ログインしていない場合はログインページにリダイレクト
  //  header('Location: login.php');
  //  exit();


// 初期値
$uploaded_image = null; // 初期化せず、一旦 null を設定
$profile = '';
$user_id = $_SESSION['id']; // セッションから user_id を取得

// データベースから現在のプロフィール情報を取得
try {
    $sql = "SELECT profile_img, profile FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $uploaded_image = $result['profile_img'] ?? null;
        $profile = $result['profile'] ??'';
    }
} catch (PDOException $e) {
    echo 'エラー: ' . $e->getMessage();
}

// データベースから取得できなかった場合のみデフォルト値を設定
if (!$uploaded_image) {
    $uploaded_image = 'img/user.png';
}

// // POSTリクエスト時に更新処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // デバッグ用
    error_log('POST request received');
    error_log('FILES: ' . print_r($_FILES, true));
    
    $profile = !empty($_POST['profile']) ? $_POST['profile'] : '';
    $uploadOk = 1;
    $targetFile = $uploaded_image;

    // 画像アップロード時に処理を実施
    if (!empty($_FILES['profile_img']['name'])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . uniqid() . "_" . basename($_FILES["profile_img"]["name"]);
        
        // 画像ファイルの確認と処理
        $check = getimagesize($_FILES["profile_img"]["tmp_name"]);
        if ($check !== false) {
            if ($_FILES["profile_img"]["size"] <= 500000) {
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                    if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $targetFile)) {
                        $uploaded_image = $targetFile; // ここを修正：フルパスを保存
                    } else {
                        echo "画像のアップロードに失敗しました。<br>";
                        exit();
                    }
                } else {
                    echo "許可されていないファイル形式です。<br>";
                }
            } else {
                echo "ファイルサイズが大きすぎます。<br>";
            }
        } else {
            echo "画像ファイルではありません。<br>";
        }
    }

    // データベースに更新
    try {
        $sql = "UPDATE users SET profile_img = :profile_img, profile = :profile WHERE user_id = :user_id";

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
    <link rel="stylesheet" href="css/usersettei.css?1">
</head>
<body>
    <div class="fullscreen-image">
        <img src="img/haikei4.png" alt="Full Screen Image">
        <div class="container">
            <a href="#" class="back-button" onclick="goBack()">←戻る</a>
            <form action="" method="POST" id="profile-form" enctype="multipart/form-data">
            <!-- プロフィール画像のプレビュー -->
            <div class="user-image" id="profile-image-preview" style="background-image: url('<?php echo htmlspecialchars($uploaded_image); ?>');">
                <label for="image-upload" class="image-label">
                    <span class="plus-icon">+</span>
                    <input type="file" id="image-upload" name="profile_img" style="display:none;" accept="image/*" onchange="previewAndUploadImage(event)">
                </label>
            </div>

            
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
    </script>
</body>
</html>
