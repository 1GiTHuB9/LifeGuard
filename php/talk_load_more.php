<?php
session_start();
require "dbConnect.php";
// モデルクラスを読み込む
require "talk_model.php";
// モデルクラスのインスタンスを生成
$model = new Talk_model($pdo);

// 自分のID
$user1_id = $_SESSION['id'];
$room_id = $_GET['room_id'];
$offset = (int)$_GET['offset'];

$limit = 50; // 一度に読み込む件数

try {
    // 対象の user2_id を userrooms テーブルから取得
    $userroom = $model->get_user_room($room_id);

    if (!$userroom) {
        throw new Exception("指定されたチャットルームが見つかりません。");
    }

    $user2_id = ($userroom['user1_id'] == $user1_id) ? $userroom['user2_id'] : $userroom['user1_id'];

    // コメントとチャットを取得
    $results = $model->get_chat($room_id, $user1_id, $offset, $limit, $user2_id);

    $output = "";
    $results =array_reverse($results);
    foreach ($results as $row) {
        
        $isUser1 = ((int)$row['user_id'] === (int)$user1_id);
        $messageClass = $isUser1 ? 'right' : 'left';
        if ($row['type'] === 'comment') {
            $output .= "<div class='message center'><strong>投稿:</strong> " . htmlspecialchars($row['post_detail']) . "</div>";
            $output .= "<div class='message $messageClass'><strong>コメント:</strong> " . htmlspecialchars($row['detail']) . "</div>";
        } elseif ($row['type'] === 'chat') {
            $output .= "<div class='message $messageClass'>" . htmlspecialchars($row['detail']) . "</div>";
        }
    }

    echo $output;

} catch (Exception $e) {
    echo "<p>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
}
