<?php
$dsn = 'mysql:host=localhost;dbname=lifeguard;charset=utf8'; // DSNを設定
$username = 'lifeguard_user'; // ユーザー名
$password = 'Liguardfe712'; // パスワード（デフォルトは空白）

try {
    // PDOインスタンスの作成
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを設定
} catch (PDOException $e) {
    die("接続失敗: " . $e->getMessage());
}
?>
