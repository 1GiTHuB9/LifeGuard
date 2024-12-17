<?php
session_start();
require 'dbConnect.php';
//削除ボタンを押した際の処理

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room_id'])) {
    $roomId = (int) $_GET['room_id'];

    // 外部キーに影響するテーブルを削除
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("DELETE FROM chats WHERE room_id = :room_id");
        $stmt->execute([':room_id' => $roomId]);

        $stmt = $pdo->prepare("DELETE FROM userrooms WHERE room_id = :room_id");
        $stmt->execute([':room_id' => $roomId]);

        $stmt = $pdo->prepare("DELETE FROM chatrooms WHERE room_id = :room_id");
        $stmt->execute([':room_id' => $roomId]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("エラーが発生しました: " . $e->getMessage());
    }
    header('Location: ../talkitiran.php');
    exit();
}
