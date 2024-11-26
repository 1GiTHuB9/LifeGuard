<?php
session_start();
require "dbConnect.php";

// 自分のID
$user1_id = $_SESSION['id'];
$room_id = $_GET['room_id'];
$offset = (int)$_GET['offset'];

$limit = 50; // 一度に読み込む件数

try {
    // 対象の user2_id を userrooms テーブルから取得
    $stmt = $pdo->prepare("
        SELECT user1_id, user2_id
        FROM userrooms
        WHERE room_id = ?
    ");
    $stmt->execute([$room_id]);
    $userroom = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userroom) {
        throw new Exception("指定されたチャットルームが見つかりません。");
    }

    $user2_id = ($userroom['user1_id'] == $user1_id) ? $userroom['user2_id'] : $userroom['user1_id'];

    // コメントとチャットを取得
    $stmt = $pdo->prepare("
        SELECT * FROM (
            SELECT 
                c.comment_date AS date,
                c.comment_detail AS detail,
                p.post_detail AS post_detail,
                c.user_id,
                'comment' AS type
            FROM comments c
            JOIN posts p ON c.post_id = p.post_id
            WHERE (c.user_id = :user1_id AND p.user_id = :user2_id)
               OR (c.user_id = :user2_id AND p.user_id = :user1_id)

            UNION ALL

            SELECT 
                ch.talk_date AS date,
                ch.talk_detail AS detail,
                NULL AS post_detail,
                ch.user_id,
                'chat' AS type
            FROM chats ch
            WHERE ch.room_id = :room_id
        ) AS combined
        ORDER BY date DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':user1_id', $user1_id, PDO::PARAM_INT);
    $stmt->bindValue(':user2_id', $user2_id, PDO::PARAM_INT);
    $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
