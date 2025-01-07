<?php
    session_start();
    require "./php/dbConnect.php";
    require "./php/talk_model.php";
    // モデルのインスタンス化
    $model = new Talk_model($pdo);
    
    // 拒否されたチャットルームを取得
    try {
        $rejectedChatRooms = $model->getRejectedChatRooms($_SESSION['id']);
    } catch (Exception $e) {
        $error = "データ取得中にエラーが発生しました: " . htmlspecialchars($e->getMessage());
    }
    ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トーク一覧画面</title>
    <link rel="stylesheet" href="css/talkitiran.css?v=<?php echo filemtime('./css/talkitiran.css'); ?>">
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
        <div class="container">
            <a href="#" class="back-button" onclick="backTalkitiran()">←戻る</a>    
            
            <?php if (!empty($error)): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php else: ?>
            <!-- 未承認チャット出力-->
                <?php if (!empty($rejectedChatRooms)): ?>
                    <h1></h1>
                    <?php foreach ($rejectedChatRooms as $room): ?>
                        <div class="pending-message">
                            <!-- 投稿 -->
                            <p class="username"><?= htmlspecialchars($room['post_detail']) ?></p>
                            <p class="post-title">コメント: <?= htmlspecialchars($room['comment_detail']) ?></p>
                            <p class="comment-detail">コメントしたユーザー: <?= htmlspecialchars($room['user_name']) ?></p>
                            <!-- ボタン -->
                            <div class="button-container">    
                                <button class="approve-button" onclick="confirmApproval(<?= $room['room_id'] ?>)">承認する</button>
                                <button class="delete-button" onclick="confirmDeletion(<?= $room['room_id'] ?>)">削除する</button>
                            </div>
                        </div>
                        <hr class="divider">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>保留したチャットルームはありません。</p>
                <?php endif; ?>

            <?php endif; ?>
        </div>

    </div>
    <!-- 承認ボタンなどのjs -->
    <script src="./js/talkitiran.js?v=<?php echo filemtime('./js/talkitiran.js'); ?>"></script>
    <script>
        // talkitirn.phpへ戻るボタン
        function backTalkitiran() {
            location.href = "talkitiran.php";
        }
    </script>
</body>
</html>