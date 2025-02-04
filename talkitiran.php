<?php
    session_start();
    require "./php/dbConnect.php";
    require "./php/talk_model.php";
    // モデルのインスタンス化
    $model = new Talk_model($pdo);
    // セッションにログイン情報がない場合はログイン画面にリダイレクト
    if (!isset($_SESSION['id'])) {
        header('Location: login.php');
        exit();
    }
    try {
        // 未承認と承認済みのチャットルームを取得
        $pendingRooms = $model->getPendingChatRooms($_SESSION['id']);
        $approvedRooms = $model->getApprovedChatRooms($_SESSION['id']);
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
            <a href="#" class="back-button" onclick="goBack()">←戻る</a>    
            
            <?php if (!empty($error)): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php else: ?>
            <!-- 未承認チャット出力-->
                <?php if (!empty($pendingRooms)): ?>
                    <h1></h1>
                    <?php foreach ($pendingRooms as $room): ?>
                        <div class="pending-message">
                            <!-- 投稿 -->
                            <p class="username"><?= htmlspecialchars($room['post_detail']) ?></p>
                            <p class="post-title">コメント: <?= htmlspecialchars($room['comment_detail']) ?></p>
                            <p class="comment-detail">コメントしたユーザー: <?= htmlspecialchars($room['user_name']) ?></p>
                            <!-- ボタン -->
                            <div class="button-container">    
                                <button class="approve-button" onclick="confirmApproval(<?= $room['room_id'] ?>)">承認する</button>
                                <button class="reject-button" onclick="confirmRejection(<?= $room['room_id'] ?>)">保留する</button>
                                <button class="delete-button" onclick="confirmDeletion(<?= $room['room_id'] ?>)">削除する</button>
                            </div>
                        </div>
                        <hr class="divider">
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- <p>未承認のチャットルームはありません。</p> -->
                <?php endif; ?>

                <?php if (!empty($approvedRooms)): ?>
                    <!-- 承認チャット出力 -->
                    <?php foreach ($approvedRooms as $room): ?>
                        <div class="message">
                            <!-- 画像 -->
                            <div class="profile-pic"></div>
                            <div class="message-content" onclick="location.href='talk.php?room_id=<?= htmlspecialchars($room['room_id']) ?>';">
                                <p class="username"><?= htmlspecialchars($room['room_name']) ?></p>
                                <!-- 最新チャット -->
                                <p class="text"><?= htmlspecialchars($room['latest_message'] ?? '　　　　　　　　　　') ?></p>
                            </div>
                        </div>
                        <hr class="divider">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>承認済みのチャットルームはありません。</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <a href="talk_reject_list.php" class="reject-list-button">保留したチャットリストを見る</a>

    </div>
    <!-- 戻るボタン、承認ボタンなどのjs -->
    <script src="./js/talkitiran.js?v=<?php echo filemtime('./js/talkitiran.js'); ?>"></script>
</body>
</html>