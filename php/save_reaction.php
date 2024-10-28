<?php
require "./dbConnect.php";

// POSTデータを取得
$calendar_id = $_POST['calendar_id'];
$user_id = $_POST['user_id'];
$reaction = $_POST['reaction']; // VARCHARなのでそのまま
$reaction_date = $_POST['reaction_date']; // DATE型

// SQL文を準備して実行
$sql = "INSERT INTO calendars (calendar_id, user_id, reaction, reaction_date) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $calendar_id, $user_id, $reaction, $reaction_date); // VARCHARにはsを使用

if ($stmt->execute()) {
    echo "Record saved successfully";
} else {
    echo "Error: " . $stmt->error;
}

// 接続を閉じる
$stmt->close();
$conn->close();
?>