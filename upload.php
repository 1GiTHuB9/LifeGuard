<?php
session_start();
require "./php/dbConnect.php"; // データベース接続

// ユーザーIDをセッションから取得
$userid = $_SESSION['user_id'] ?? null;

// POSTリクエストで画像がアップロードされた場合
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_img']) && $userid) {
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
            try {
                // 既存の画像データを削除
                $sql = "SELECT file_name FROM uploaded_images WHERE user_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$userid]);
                $existingImage = $stmt->fetchColumn();

                if ($existingImage && file_exists($uploadDir . $existingImage)) {
                    unlink($uploadDir . $existingImage); // 古いファイルを削除
                }

                // 新しい画像データを挿入または更新
                $sql = "INSERT INTO uploaded_images (user_id, file_name) 
                        VALUES (:user_id, :file_name) 
                        ON DUPLICATE KEY UPDATE file_name = :file_name";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_id', $userid, PDO::PARAM_INT);
                $stmt->bindParam(':file_name', $imageName, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "image" => $imageName]);
                } else {
                    echo json_encode(["success" => false, "error" => "データベースへの保存に失敗しました。"]);
                }
            } catch (PDOException $e) {
                echo json_encode(["success" => false, "error" => "エラー: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "アップロードに失敗しました。"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "無効なファイル形式です。"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "画像が見つからないか、ユーザーIDがありません。"]);
}
?>
