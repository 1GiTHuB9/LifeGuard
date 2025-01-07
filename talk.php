<?php
    session_start();
    require "./php/dbConnect.php";
    // モデルクラスを読み込む
    require "./php/talk_model.php";
    // セッションにログイン情報がない場合はログイン画面にリダイレクト
    if (!isset($_SESSION['id'])) {
        header('Location: login.php');
        exit();
    }
    // モデルクラスのインスタンスを生成
    $model = new Talk_model($pdo);
    // 自分のID
    $user1_id = $_SESSION['id'];
    // メッセージを変数に格納
    $output = "";
    // チャットルームのIDが指定されている場合
    if (isset($_GET['room_id'])) {
        $room_id = $_GET['room_id'];
        try {
            // 1. チャットルーム名を取得
            $room = $model->get_room_name($room_id);
            $room_name = $room['room_name'] ?? null;
            // 対象の user2_id を userrooms テーブルから取得
            $userroom = $model->get_user_room($room_id);
    
            if (!$userroom) {
                throw new Exception("指定されたチャットルームが見つかりません。");
            }
    
            // 自分が user1 なら相手は user2、それ以外なら user1
            $user2_id = ($userroom['user1_id'] == $user1_id) ? $userroom['user2_id'] : $userroom['user1_id'];
            //読み込み件数
            $limit=50;
            //読み込み開始位置
            $offset =0;
            // コメントとチャットを取得（投稿も含む）
            $results = $model->get_chat($room_id, $user1_id, $offset,$limit, $user2_id);
    
            if ($results) {
                //DESCで取得しているため、逆順に表示
                $results = array_reverse($results);
                foreach ($results as $row) {
                    // ユーザーが自分かどうかで表示を変える
                    $isUser1 = ((int)$row['user_id'] === (int)$user1_id);
                    $messageClass = $isUser1 ? 'right' : 'left';
                    //コメントの場合
                    if ($row['type'] === 'comment') {
                        // 投稿とコメントを表示
                        $output .= "<div class='message center'><strong>投稿:</strong> " . htmlspecialchars($row['post_detail']) . "</div>";
                        $output .= "<div class='message $messageClass'><strong>コメント:</strong> " . htmlspecialchars($row['detail']) . "</div>";
                    } elseif ($row['type'] === 'chat') {
                        // チャットを表示
                        $output .= "<div class='message $messageClass'>" . htmlspecialchars($row['detail']) . "</div>";
                    }
                }
    
            } else {
                echo "<div class='message'>データが見つかりませんでした。</div>";
            }
        } catch (Exception $e) {
            echo "<p>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p>room_id が指定されていません。</p>";
    }
    // チャットの送信処理
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['data'])) {
        $data = trim($_POST['data']);
        $room_id = $_GET['room_id'] ?? null;
    
        if ($room_id && $data !== "") {
            try {
                $model->send_chat($user1_id, (int)$room_id, $data);
                // 再読み込み
                header("Location: " . $_SERVER['PHP_SELF'] . "?room_id=" . $room_id);
                exit();
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                //コンソールにエラーメッセージを表示
                echo "<script>console.log('エラー: " . htmlspecialchars($error_message) . "');</script>";
            }
        } else {
            $error_message = "メッセージが空です。";
            //アラートでエラーメッセージを表示
            echo "<script>alert('エラー: " . htmlspecialchars($error_message) . "');</script>";
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
            <?php
            // チャット内容を表示
                echo $output;
            ?>
        </div>
        <form action="" method="POST" class="input-area">
            <input type="text" name="data" placeholder="メッセージを入力・・・" required>
            <button type="submit" class="send-btn">送信</button>
        </form>
    </div>
    </div>

    <script>
        function goBack() {
            location.href = "talkitiran.php";
        }    
        function scrollToBottom() {
        const chatWindow = document.querySelector('.chat-window');
        chatWindow.scrollTop = chatWindow.scrollHeight; // 一番下にスクロールする
        }
        window.onload = function() {
            scrollToBottom(); // ページ読み込み時に最下位にスクロール
        }
        // チャット追加読み込み用の変数 ルームIDを設定
        const room_id = <?php echo json_encode($room_id); ?>;
    </script>
    <!--チャット追加読み込みjs-->
    <script src="./js/talk.js?v=<?php echo filemtime('./js/talk.js'); ?>"></script>
</body>
</html>
