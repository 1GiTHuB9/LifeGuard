<?php

session_start();
require "./dbConnect.php"; // データベース接続

// ページ番号を取得し、デフォルトを0に設定
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$post_id = (int) $_GET['post_id'];
$limit = 10;
$offset = $page * $limit;

// データ取得クエリ
$sql = "SELECT c.comment_detail, c.comment_date, c.comment_flag, u.user_name, u.profile_img
        FROM comments AS c
        LEFT JOIN users AS u ON c.user_id = u.user_id
        WHERE c.post_id = ?
        ORDER BY c.comment_date DESC
        LIMIT ? OFFSET ?";

$stmt = $pdo->prepare($sql); 

$stmt->bindValue(1, $post_id, PDO::PARAM_INT);
$stmt->bindValue(2, $limit, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);

// クエリ実行
$stmt->execute();

$comment = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($comment);

?>