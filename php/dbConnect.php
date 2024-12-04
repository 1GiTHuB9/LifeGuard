<?php
// データベース接続
$host = 'localhost';
$dbname = 'lifeguard2';
$username = 'lifeguard_user';
$password = 'Liguardfe712';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
