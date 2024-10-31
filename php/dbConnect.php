<?php
// データベース接続
$host = 'localhost';
$dbname = 'lifeguard';
$username = 'lifeguard_user';
// データベース接続（本番環境）
// $host = 'mysql-3.mc.lolipop.lan';
// $dbname = '3caf596e27571a5610d17d2e8bf5e786';
// $username = '3caf596e27571a5610d17d2e8bf5e786';
$password = 'Liguardfe712';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
