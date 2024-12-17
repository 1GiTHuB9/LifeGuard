<?php
session_start();
require "dbConnect.php";
//ログインしているユーザーのIDを取得
$user_id = $_SESSION['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_detail = $_POST['comment_detail'];
    $comment_flag = $_POST['comment_flag'];
    $post_id = $_POST['post_id'];
    $comment_user_name = $_POST['comment_user_name'];
    // コメント投稿先のユーザーIDを取得
    $post_user_id = $_POST['post_user_id'];
    $user_name=$_POST['user_name'];

    try {
        // トランザクション開始
        $pdo->beginTransaction();

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

        // UserRooms テーブルに user_id と post_user_id の組み合わせが存在するか確認
        $stmt = $pdo->prepare("
            SELECT room_id FROM userrooms 
            WHERE (user1_id = :user_id AND user2_id = :post_user_id)
               OR (user1_id = :post_user_id AND user2_id = :user_id)
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':post_user_id', $post_user_id, PDO::PARAM_INT);
        $stmt->execute();

        // チャットルームが存在しない場合、新しいルームを作成
        if ($stmt->rowCount() === 0&&(int)$user_id !== (int)$post_user_id) {
            // チャットルーム名を生成
            if ($user_name === "匿名" && $comment_user_name === "匿名") {
                $room_name = '匿名同士のチャット';
            } else {
                $room_name = $user_name . "と" . $comment_user_name."のチャット";
            }

            // 新しいルームを ChatRooms テーブルに作成
            $stmt = $pdo->prepare("
                INSERT INTO chatrooms (room_name, room_date) 
                VALUES (:room_name, CURDATE())
            ");
            $stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
            $stmt->execute();

            // 新しく作成した room_id を取得
            $room_id = $pdo->lastInsertId();

            // UserRooms テーブルにユーザーの組み合わせと room_id を挿入
            $stmt = $pdo->prepare("
                INSERT INTO userrooms (user1_id, user2_id, room_id) 
                VALUES (:user_id, :post_user_id, :room_id)
            ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':post_user_id', $post_user_id, PDO::PARAM_INT);
            $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        // トランザクション完了
        $pdo->commit();

        // リダイレクト
        header("Location: ../eturan.php");  // 投稿成功ページへリダイレクト
        exit();
    } catch (PDOException $e) {
        // エラー時にロールバック
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    echo "<p>不正なアクセスです。</p>";
}
?>
