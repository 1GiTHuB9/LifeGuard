<?php
// $dsn = 'mysql:host=localhost;dbname=lifeguard;charset=utf8'; // DSNを設定
// $username = 'lifeguard_user'; // ユーザー名
$password = 'Liguardfe712'; // パスワード（デフォルトは空白）
//本番環境
    $dsn = 'mysql:host=mysql-3.mc.lolipop.lan;dbname=3caf596e27571a5610d17d2e8bf5e786;charset=utf8';
    $username = '3caf596e27571a5610d17d2e8bf5e786';
try {
    // PDOインスタンスの作成
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを設定
} catch (PDOException $e) {
    die("接続失敗: " . $e->getMessage());
}
?>
