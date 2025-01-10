<?php
session_start();
require 'dbConnect.php';
//承認ボタンを押した際の処理

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room_id'])) {
    $roomId = (int) $_GET['room_id'];
    // チャットルームの承認ステータスを承認済みに変更
    $stmt = $pdo->prepare("UPDATE chatrooms SET approval_status = 1, approved_by = :user_id WHERE room_id = :room_id");
    $stmt->execute([
        ':user_id' => $_SESSION['id'],
        ':room_id' => $roomId,
    ]);
    header('Location: ../talkitiran.php');
    exit();
}
