<?php
session_start();
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
            // ユーザーIDをセッションから取得
            $userId = $_SESSION['user_id'] ?? null;

            if ($userId) {
                // データベースにファイル名を保存
                $sql = "UPDATE users SET profile_img = :profile_img WHERE user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':profile_img', $imageName, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "image" => $imageName]);
                } else {
                    echo json_encode(["success" => false, "error" => "データベースの更新に失敗しました。"]);
                }
            } else {
                echo json_encode(["success" => false, "error" => "ユーザーがログインしていません。"]);
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
