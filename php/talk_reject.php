<?php
session_start();
require 'dbConnect.php';
//保留ボタンを押した際の処理

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room_id'])) {
    $roomId = (int) $_GET['room_id'];
    // チャットルームの承認ステータスを保留に変更
    $stmt = $pdo->prepare("UPDATE chatrooms SET approval_status = 2 WHERE room_id = :room_id");
    $stmt->execute([':room_id' => $roomId]);
    header('Location: ../talkitiran.php');
    exit();
}
