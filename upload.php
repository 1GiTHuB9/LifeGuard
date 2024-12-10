<?php
require "./php/dbConnect.php"; // データベース接続

// POSTリクエストで画像がアップロードされた場合
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_img'])) {
    $fileType = $_FILES['profile_img']['type'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

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
            // アップロードが成功した場合、データベースにファイル名を保存
            $sql = "INSERT INTO uploaded_images (file_name) VALUES (:file_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':file_name', $imageName, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "image" => $imageName]);
            } else {
                echo json_encode(["success" => false, "error" => "データベースへの保存に失敗しました。"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "アップロードに失敗しました。"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "無効なファイル形式です。"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "画像が見つかりません。"]);
}
?>