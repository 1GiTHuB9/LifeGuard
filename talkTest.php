<?php
session_start();
require "./php/dbConnect.php";

$user_id = $_SESSION['id']; // 自分のID

if (isset($_GET['room_id'])) {
    $room_id = (int)$_GET['room_id'];
} else {
    echo "ルームIDが指定されていません。";
    exit();
}

// 相手のuser_idを特定
try {
    $stmt = $pdo->prepare("SELECT user1_id, user2_id FROM userrooms WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $userroom = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userroom) {
        $other_user_id = ($userroom['user1_id'] === $user_id) ? $userroom['user2_id'] : $userroom['user1_id'];
    } else {
        echo "チャットルームが見つかりません。";
        exit();
    }
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
    exit();
}

// データを取得
try {
    $query = "
        SELECT 'chat' AS type, talk_detail AS detail, talk_date AS date, user_id
        FROM chats
        WHERE room_id = :room_id
        UNION ALL
        SELECT 'comment' AS type, CONCAT(posts.post_detail, '\n', comments.comment_detail) AS detail, comments.comment_date AS date, comments.user_id
        FROM comments
        INNER JOIN posts ON comments.post_id = posts.post_id
        WHERE (comments.user_id = :user_id AND posts.user_id = :other_user_id)
           OR (comments.user_id = :other_user_id AND posts.user_id = :user_id)
        ORDER BY date ASC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':other_user_id', $other_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage();
    exit();
}

// メッセージを変数に格納
$output = "";
foreach ($messages as $message) {
    if ($message['type'] === 'chat') {
        $messageClass = ((int)$message['user_id'] === (int)$user_id) ? 'right' : 'left';
    } else if ($message['type'] === 'comment') {
        $messageClass = 'center';
    }
    $output .= "<div class='message $messageClass'>" . htmlspecialchars($message['detail']) . "</div>";
}

// 結果を出力
echo $output;
?>
