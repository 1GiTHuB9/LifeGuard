<?php
function commentSelectById($id){
    $sql = "SELECT u.user_id,u.user_name,c.comment_id,c.comment_detail,c.comment_date,c.comment_flag FROM comments as c LEFT OUTER JOIN users as u ON c.user_id = u.user_id ORDER BY comment_date ASC";
        
        $stmt = $pdo->prepare($sql); 
        
        // クエリ実行
        $stmt->execute();

        $post = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $post;
}
?>