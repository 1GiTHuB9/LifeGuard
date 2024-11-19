<?php
session_start();
require "./php/dbConnect.php";
$user_id = $_SESSION['id'];

if (!isset($_GET['room_id'])) {
    echo "<p>ルームIDが指定されていません。</p>";
    exit();
}
$room_id = $_GET['room_id'];

try {
    // 投稿を取得
    $sqlPosts = "
        SELECT p.post_detail, p.post_date, p.user_id
        FROM posts p
        JOIN userrooms ur ON (p.user_id = ur.user1_id OR p.user_id = ur.user2_id)
        WHERE ur.room_id = ?
        ORDER BY p.post_date ASC
    ";
    $stmtPosts = $pdo->prepare($sqlPosts);
    $stmtPosts->execute([$room_id]);
    $posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);

    // コメントを取得
    $sqlComments = "
        SELECT c.comment_detail, c.comment_date, c.user_id
        FROM comments c
        JOIN posts p ON c.post_id = p.post_id
        JOIN userrooms ur ON (p.user_id = ur.user1_id OR p.user_id = ur.user2_id)
        WHERE ur.room_id = ?
        ORDER BY c.comment_date ASC
    ";
    $stmtComments = $pdo->prepare($sqlComments);
    $stmtComments->execute([$room_id]);
    $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

    // 投稿とコメントを統合して時系列順に並び替え
    $timeline = [];
    foreach ($posts as $post) {
        $timeline[] = [
            'type' => 'post',
            'detail' => $post['post_detail'],
            'date' => $post['post_date'],
            'user_id' => $post['user_id']
        ];
    }
    foreach ($comments as $comment) {
        $timeline[] = [
            'type' => 'comment',
            'detail' => $comment['comment_detail'],
            'date' => $comment['comment_date'],
            'user_id' => $comment['user_id']
        ];
    }

    // 日付順に並び替え
    usort($timeline, function ($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });

    // 表示
    foreach ($timeline as $item) {
        if ($item['type'] === 'post') {
            $messageClass = 'center'; // 投稿は中央表示
        } else {
            $messageClass = ((int)$item['user_id'] === (int)$user_id) ? 'right' : 'left';
        }
        echo "<div class='message $messageClass'>" . htmlspecialchars($item['detail']) . "</div>";
    }
} catch (PDOException $e) {
    echo "<p>エラー: " . $e->getMessage() . "</p>";
}
?>
