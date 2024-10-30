<?php
session_start();
require "dbConnect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_detail = $_POST['comment_detail'];
    $comment_flag = $_POST['comment_flag'];
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    try {
        // コメントを comments テーブルに挿入
        $stmt = $pdo->prepare("
            INSERT INTO comments (comment_detail, comment_date, comment_flag, post_id, user_id)
            VALUES (:comment_detail, NOW(), :comment_flag, :post_id, :user_id)
        ");
        $stmt->bindParam(':comment_detail', $comment_detail, PDO::PARAM_STR);
        $stmt->bindParam(':comment_flag', $comment_flag, PDO::PARAM_INT);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // 挿入完了後、完了メッセージやリダイレクトを設定
        // echo "<p>コメントが投稿されました。</p>";
        header("Location: ../eturan.php");  // 投稿成功ページへリダイレクト
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    echo "<p>不正なアクセスです。</p>";
}
?>
