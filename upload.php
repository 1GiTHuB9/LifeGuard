<?php
require './php/dbConnect.php'; // データベース接続ファイルをインクルード

// フォームが送信された場合の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 画像アップロード処理
    $uploadDir = 'uploads/'; // 画像を保存するフォルダ
    $uploadFile = $uploadDir . basename($_FILES['profile_img']['name']);

    if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $uploadFile)) {
        // データベースに情報を保存
        $stmt = $pdo->prepare("INSERT INTO users (mailaddress, password, user_name, profile_img, profile, diagnosis_level)
                               VALUES (:mailaddress, :password, :user_name, :profile_img, :profile, :diagnosis_level)");
        $stmt->execute([
            ':mailaddress' => $_POST['mailaddress'],
            ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            ':user_name' => $_POST['user_name'],
            ':profile_img' => $uploaded_image, // アップロードされた画像のフルパスを使用
            ':profile' => $_POST['profile'],
            ':diagnosis_level' => (int)$_POST['diagnosis_level']
        ]);

        echo "登録成功！";
    } else {
        echo "画像のアップロードに失敗しました。";
    }
}
?>
