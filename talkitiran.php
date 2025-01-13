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
    <link rel="stylesheet" href="css/talkitiran.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
        <div class="container">
            <a href="#" class="back-button" onclick="goBack()">←戻る</a>    
            
            <?php if (!empty($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php else: ?>
                <!-- 未承認チャット出力 -->
                <!-- 未承認チャット出力 -->
                <?php if (!empty($pendingRooms)): ?>
                    <h2>未承認のチャット</h2>
                <?php foreach ($pendingRooms as $room): ?>
                    <div class="pending-message">
                    <div class="profile-pic">
                        <?php if (!empty($room['profile_img'])): ?>
                            <img src="<?= htmlspecialchars($room['profile_img']) ?>" alt="Profile Image" class="profile-image">
                        <?php else: ?>
                            <img src="./img/user.png" alt="Default Profile Image" class="profile-image">
                        <?php endif; ?>
                    </div>
                    <div class="message-content">
                        <p class="post-detail"><?= htmlspecialchars($room['post_detail']) ?></p>
                        <p class="comment-detail">コメント: <?= htmlspecialchars($room['comment_detail']) ?></p>
                        <p class="user-name">From: <?= htmlspecialchars($room['user_name']) ?></p>
                    </div>
                    <div class="button-container">    
                        <button class="approve-button" onclick="confirmApproval(<?= $room['room_id'] ?>)">承認する</button>
                        <button class="reject-button" onclick="confirmRejection(<?= $room['room_id'] ?>)">保留する</button>
                        <button class="delete-button" onclick="confirmDeletion(<?= $room['room_id'] ?>)">削除する</button>
                    </div>
                    </div>
                    <hr class="divider">
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- 承認済みチャット出力 -->
                <!-- 承認済みチャット出力部分 -->
                <?php if (!empty($approvedRooms)): ?>
                    <h2>承認済みのチャット</h2>
                <?php foreach ($approvedRooms as $room): ?>
                <div class="message" onclick="location.href='talk.php?room_id=<?= htmlspecialchars($room['room_id']) ?>';">
                    <div class="profile-pic">
                        <?php if (!empty($room['profile_img'])): ?>
                            <img src="<?= htmlspecialchars($room['profile_img']) ?>" alt="Profile Image" class="profile-image">
                        <?php else: ?>
                            <img src="./img/user.png" alt="Default Profile Image" class="profile-image">
                        <?php endif; ?>
                    </div>
                <div class="message-content">
                    <p class="username"><?= htmlspecialchars($room['user_name']) ?></p>
                    <p class="text"><?= htmlspecialchars($room['comment_detail'] ?? '') ?></p>
                <?php if (!empty($room['latest_message'])): ?>
                    <p class="latest-message"><?= htmlspecialchars($room['latest_message']) ?></p>
                <?php endif; ?>
            </div>
        </div>
                <hr class="divider">
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-chats">承認済みのチャットルームはありません。</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <a href="talk_reject_list.php" class="reject-list-button">保留したチャットリストを見る</a>
    </div>
    <script src="./js/talkitiran.js?v=<?php echo time(); ?>"></script>
</body>
</html>