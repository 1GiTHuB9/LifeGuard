<?php
session_start();
require "./php/dbConnect.php"; // データベース接続

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

    if (in_array($fileType, $allowedTypes)) {
        // ユニークなファイル名を生成
        $extension = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('profile_', true) . '.' . $extension;
        $uploadDir = 'uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // アップロード先にファイルを保存
        if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $uploadDir . $imageName)) {
            try {
                // 既存の画像データを削除
                $sql = "SELECT file_name FROM uploaded_images WHERE user_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$userid]);
                $existingImage = $stmt->fetchColumn();

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
        $sql = "UPDATE Users SET profile_img = :profile_img, profile = :profile, is_anonymous = :is_anonymous WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        
        // プレースホルダーに値をバインド
        $stmt->bindParam(':profile_img', $uploaded_image, PDO::PARAM_STR);
        $stmt->bindParam(':profile', $profile, PDO::PARAM_STR);
        $stmt->bindParam(':is_anonymous', $isAnonymous, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // SQLを実行
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'image' => $uploaded_image]);
        } else {
            echo json_encode(["success" => false, "error" => "アップロードに失敗しました。"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "無効なファイル形式です。"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "画像が見つからないか、ユーザーIDがありません。"]);
}
}
}
}

?>
