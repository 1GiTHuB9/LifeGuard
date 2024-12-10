<?php
require './php/dbConnect.php'; // データベース接続ファイルをインクルード

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームからのデータ取得
    $user_id = $_POST['user_id'];
    $profile = !empty($_POST['profile']) ? $_POST['profile'] : ''; // 空の場合は空文字列にする
    $isAnonymous = isset($_POST['anonymous']) ? 1 : 0; // チェックボックスの値を整数に変換
    $uploaded_image = !empty($_POST['uploaded_image']) ? $_POST['uploaded_image'] : ''; // 空の場合は空文字列にする

    // 画像がアップロードされたかどうか確認
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
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo json_encode(['success' => false, 'error' => 'JPG、JPEG、PNG、GIFファイルのみが許可されています。']);
            $uploadOk = 0;
        }

        // エラーがない場合、ファイルをアップロード
        if ($uploadOk === 1) {
            if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $targetFile)) {
                $uploaded_image = basename($_FILES["profile_img"]["name"]); // アップロードされた画像名を保存
            } else {
                echo json_encode(['success' => false, 'error' => '画像のアップロードに失敗しました。']);
            }
        }
    }

    // データベースのユーザー情報を更新
    try {
        $sql = "UPDATE Users SET profile_img = :profile_img, profile = :profile,  WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        
        // プレースホルダーに値をバインド
        $stmt->bindParam(':profile_img', $uploaded_image, PDO::PARAM_STR);
        $stmt->bindParam(':profile', $profile, PDO::PARAM_STR);
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
