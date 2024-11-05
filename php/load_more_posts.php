<?php
session_start();

require "./dbConnect.php";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([]);
    exit();
}

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

$sql = "SELECT u.user_id, u.user_name, p.post_id, p.post_detail, p.post_date, p.post_flag 
        FROM posts AS p 
        LEFT OUTER JOIN users AS u ON p.user_id = u.user_id 
        ORDER BY post_date ASC 
        LIMIT :offset, :limit";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($posts);
?>