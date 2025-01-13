<?php
session_start();
require "./php/dbConnect.php";
require "./php/talk_model.php";

// セッションチェック
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// モデルクラスのインスタンスを生成
$model = new Talk_model($pdo);
$user1_id = $_SESSION['id'];
$output = "";

// チャットルームのIDが指定されている場合
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];
    try {
        // チャットルーム情報を取得
        $room = $model->get_room_name($room_id);
        $room_name = $room['room_name'] ?? null;
        
        // ユーザールーム情報を取得
        $userroom = $model->get_user_room($room_id, $user1_id);

        if (!$userroom) {
            throw new Exception("指定されたチャットルームが見つかりません。");
        }

        //読み込み件数とオフセット
        $limit = 50;
        $offset = 0;
        
        // チャット履歴を取得
        $results = $model->get_chat($room_id, $user1_id, $offset, $limit);

        if ($results) {
            $results = array_reverse($results);
            foreach ($results as $row) {
                $isUser1 = ((int)$row['user_id'] === (int)$user1_id);
                $messageClass = $isUser1 ? 'right' : 'left';
                
                if ($row['type'] === 'comment') {
                    $output .= "<div class='message center'><strong>投稿:</strong> " . htmlspecialchars($row['post_detail']) . "</div>";
                    $output .= "<div class='message $messageClass'><strong>コメント:</strong> " . htmlspecialchars($row['detail']) . "</div>";
                } elseif ($row['type'] === 'chat') {
                    $output .= "<div class='message $messageClass'>" . htmlspecialchars($row['detail']) . "</div>";
                }
            }
        } else {
            $output = "<div class='message'>データが見つかりませんでした。</div>";
        }
    } catch (Exception $e) {
        $output = "<p>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// チャットの送信処理
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['data'])) {
    $data = trim($_POST['data']);
    $room_id = $_GET['room_id'] ?? null;

    if ($room_id && $data !== "") {
        try {
            $model->send_chat($user1_id, (int)$room_id, $data);
            header("Location: " . $_SERVER['PHP_SELF'] . "?room_id=" . $room_id);
            exit();
        } catch (Exception $e) {
            echo "<script>console.log('エラー: " . htmlspecialchars($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('エラー: メッセージが空です。');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トーク画面</title>
    <link rel="stylesheet" href="css/talk.css?v=<?php echo filemtime('./css/talk.css'); ?>">
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
        <div class="chat-container">
            <div class="header">
                <a href="talkitiran.php" class="back-button">←戻る</a>
                <span class="user"><?= htmlspecialchars($room_name ?? '不明なルーム') ?></span>
            </div>
            <div class="chat-window">
                <?php echo $output; ?>
            </div>
            <form action="" method="POST" class="input-area">
                <input type="text" name="data" placeholder="メッセージを入力・・・" required>
                <button type="submit" class="send-btn">送信</button>
            </form>
        </div>
    </div>

    <script>
        function scrollToBottom() {
            const chatWindow = document.querySelector('.chat-window');
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }
        
        window.onload = function() {
            scrollToBottom();
        }
        
        const room_id = <?php echo json_encode($room_id ?? null); ?>;
    </script>
    <script src="./js/talk.js?v=<?php echo filemtime('./js/talk.js'); ?>"></script>
</body>
</html>