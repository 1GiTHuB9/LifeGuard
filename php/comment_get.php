<?php
session_start();
require "dbConnect.php"; 

// post_idの取得とページ番号の設定
if (isset($_GET['post_id']) && isset($_GET['page'])) {
    $post_id = (int) $_GET['post_id'];
    $page = (int) $_GET['page'];
    $limit = $page * 10;

    $sql = "SELECT c.comment_detail, c.comment_date, c.comment_flag, u.user_name, u.profile_img
            FROM comments AS c
            LEFT JOIN users AS u ON c.user_id = u.user_id
            WHERE c.post_id = ?
            ORDER BY c.comment_date DESC
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $post_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($comments as &$comment) {
        if ($comment['comment_flag'] == 1) {
            $comment['user_name'] = "匿名";
        }
    }

    echo json_encode($comments);
} else {
    echo json_encode(["error" => "Invalid parameters"]);
}
?>
