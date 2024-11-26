<?php
session_start();
require "./php/dbConnect.php";

$user1_id = $_SESSION['id'];
$output = "";
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

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

        // 自分が user1 なら相手は user2、それ以外なら user1
        $user2_id = ($userroom['user1_id'] == $user1_id) ? $userroom['user2_id'] : $userroom['user1_id'];

        // コメントとチャットを取得（投稿も含む）
        $stmt = $pdo->prepare("
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
            
            ORDER BY date ASC
        ");
        $stmt->execute([
            ':user1_id' => $user1_id,
            ':user2_id' => $user2_id,
            ':room_id' => $room_id,
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {

            foreach ($results as $row) {
                $isUser1 = ((int)$row['user_id'] === (int)$user1_id);
                $messageClass = $isUser1 ? 'right' : 'left';

                if ($row['type'] === 'comment') {
                    // 投稿とコメントを表示
                    $output .= "<div class='post'><strong>投稿:</strong> " . htmlspecialchars($row['post_detail']) . "</div>";
                    $output .= "<div class='message $messageClass'><strong>コメント:</strong> " . htmlspecialchars($row['detail']) . "</div>";
                } elseif ($row['type'] === 'chat') {
                    // チャットを表示
                    $output .= "<div class='message $messageClass'>" . htmlspecialchars($row['detail']) . "</div>";
                }
            }

            echo $output;
        } else {
            echo "<div class='message'>データが見つかりませんでした。</div>";
        }
    } catch (Exception $e) {
        echo "<p>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>room_id が指定されていません。</p>";
}
?>
