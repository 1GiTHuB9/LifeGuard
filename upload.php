<?php
require './php/dbConnect.php'; // データベース接続ファイルをインクルード

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームからのデータ取得
    $user_id = $_POST['user_id'];
    $profile = $_POST['profile'];
    $isAnonymous = isset($_POST['anonymous']) ? 1 : 0; // チェックボックスの値を整数に変換
    $uploaded_image = $_POST['uploaded_image'];

    // 新しい画像がアップロードされたか確認
    if (!empty($_FILES['profile_img']['name'])) {
        // ファイルアップロード処理
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["profile_img"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // 画像ファイルかどうか確認
        $check = getimagesize($_FILES["profile_img"]["tmp_name"]);
        if ($check === false) {
            echo json_encode(['success' => false, 'error' => 'ファイルは画像ではありません。']);
            $uploadOk = 0;
        }

        // ファイルサイズの確認
        if ($_FILES["profile_img"]["size"] > 500000) {
            echo json_encode(['success' => false, 'error' => 'ファイルは大きすぎます。']);
            $uploadOk = 0;
        }

        // 特定のファイル形式を許可
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo json_encode(['success' => false, 'error' => 'JPG、JPEG、PNG、GIFファイルのみが許可されています。']);
            $uploadOk = 0;
        }

        // エラーがない場合、ファイルをアップロード
        if ($uploadOk == 0) {
            echo json_encode(['success' => false, 'error' => '画像のアップロードに失敗しました。']);
        } else {
            if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $targetFile)) {
                $uploaded_image = basename($_FILES["profile_img"]["name"]); // アップロードされた画像名を保存
            } else {
                echo json_encode(['success' => false, 'error' => '画像のアップロードに失敗しました。']);
            }
        }
    }

    // データベースのユーザー情報を更新
    try {
        $sql = "UPDATE Users SET profile_img = :profile_img, profile = :profile, is_anonymous = :is_anonymous WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        
        // プレースホルダーに値をバインド
        $stmt->bindParam(':profile_img', $uploaded_image);
        $stmt->bindParam(':profile', $profile);
        $stmt->bindParam(':is_anonymous', $isAnonymous, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // SQLを実行
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'image' => $uploaded_image]);
        } else {
            echo json_encode(['success' => false, 'error' => 'データベースの更新に失敗しました。']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'データベースエラー: ' . $e->getMessage()]);
    }
}
?>
