<?php
session_start();
require "dbConnect.php"; // データベース接続

// post_idの取得
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // コメント取得クエリ
    $sql = "SELECT c.comment_detail, c.comment_date, c.comment_flag, u.user_name,u.profile_img
            FROM comments AS c
            LEFT JOIN users AS u ON c.user_id = u.user_id
            WHERE c.post_id = :post_id
            ORDER BY c.comment_date DESC
            LIMIT 10";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ユーザー名の匿名処理
    foreach ($comments as &$comment) {
        if ($comment['comment_flag'] == 1) {
            $comment['user_name'] = "匿名";
        }
    }

    // JSON形式で結果を返す
    echo json_encode($comments);
}
?>
