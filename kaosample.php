<?php
// DB接続のための設定を含む
require "./php/db.php"; // dbConnect.phpのパスを確認

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POSTデータを取得
    $user_id = $_POST['user_id'];
    $reaction = $_POST['reaction']; // VARCHARなのでそのまま
    $reaction_date = $_POST['reaction_date']; // DATE型

    // SQL文を準備
    $sql = "INSERT INTO Calendars (user_id, reaction, reaction_date) VALUES (:user_id, :reaction, :reaction_date)";
    
    try {
        // ステートメントを準備
        $stmt = $conn->prepare($sql);
        
        // パラメータをバインド
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':reaction', $reaction, PDO::PARAM_STR);
        $stmt->bindValue(':reaction_date', $reaction_date, PDO::PARAM_STR);

        // 実行
        $stmt->execute();

        echo "レコードが正常に保存されました。";
    } catch (PDOException $e) {
        echo "エラー: " . $e->getMessage();
    }

    // 接続を閉じる
    $conn = null; // PDOではnullを代入して接続を閉じる
    exit; // POSTリクエスト処理後はスクリプトを終了
}
?>
