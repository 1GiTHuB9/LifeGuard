<?php
    session_start();
    require "./php/dbConnect.php";
    require "./php/talk_model.php";
    
    $model = new Talk_model($pdo);
    
    // チャットルームと最新メッセージを取得
    try {
        $chatRooms = $model->getChatRooms();
        $latestMessages = $model->getLatestMessages();
    } catch (Exception $e) {
        $error = "データ取得中にエラーが発生しました: " . htmlspecialchars($e->getMessage());
    }
    
    // チャットルームと最新メッセージのマッピング
    $chatData = [];
    if (!empty($chatRooms) && !empty($latestMessages)) {
        foreach ($chatRooms as $index => $room) {
            $chatData[] = [
                'room_name' => $room['room_name'],
                'room_id' => $room['room_id'],
                'latest_message' => $latestMessages[$index]['talk_detail'] ?? '　　　　　　　　　　',
            ];
        }
    } elseif (empty($chatRooms)) {
        $chatData = [];
        $error = "チャットルームがありません";
    }
    ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トーク一覧画面</title>
    <link rel="stylesheet" href="css/talkitiran.css">

    


</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
    
        <div class="container">
            <a href="#" class="back-button" onclick="goBack()">←戻る</a>    
            
            <?php if (!empty($error)): ?>
            <p><?= htmlspecialchars($error) ?></p>
            <?php else: ?>
                <?php foreach ($chatData as $chat): ?>
                    <div class="message">
                        <div class="profile-pic"></div>
                        <div class="message-content" onclick="location.href='talk.php?room_id=<?= htmlspecialchars($chat['room_id']) ?>';">
                            <p class="username"><?= htmlspecialchars($chat['room_name']) ?></p>
                            <p class="text"><?= htmlspecialchars($chat['latest_message']) ?></p>
                        </div>
                    </div>
                    <hr class="divider"> <!-- 横棒を追加 -->
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function goBack() {
            location.href = "home.html";
        }    
    </script>
</body>
</html>