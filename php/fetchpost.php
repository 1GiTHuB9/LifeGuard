fetchpost

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require "./dbConnect.php"; // データベース接続

// ページ番号を取得し、デフォルトを0に設定
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$limit = 10;
$offset = $page * $limit;

// データ取得クエリ
$sql = "SELECT u.user_id, u.user_name, u.diagnosis_level, p.post_id, p.post_detail, p.post_date, p.post_flag 
        FROM posts AS p 
        LEFT OUTER JOIN users AS u ON p.user_id = u.user_id 
        WHERE u.diagnosis_level = ? 
        ORDER BY post_date ASC 
        LIMIT ? OFFSET ?";
        
$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, $_SESSION['diagnosis_level'], PDO::PARAM_INT);
$stmt->bindValue(2, $limit, PDO::PARAM_INT);
$stmt->bindValue(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON形式で結果を返す
echo json_encode($post);
