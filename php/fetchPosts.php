<?php
session_start(); // セッションの開始を追加
require_once "dbConnect.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$offset = $page * 10; // 1ページあたり10件

// プロフィール画像も含めて投稿を取得
$sql = "SELECT u.user_id, u.user_name, u.diagnosis_level, u.profile_img, 
        p.post_id, p.post_detail, p.post_date, p.post_flag 
        FROM posts as p 
        LEFT OUTER JOIN users as u ON p.user_id = u.user_id 
        WHERE u.diagnosis_level = :dlevel 
        ORDER BY post_date ASC 
        LIMIT 10 OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':dlevel', $_SESSION['dlevel'], PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($posts)) {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');
echo json_encode($posts);
?>