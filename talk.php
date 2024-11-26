<?php
    session_start();
    require "./php/dbConnect.php";
    // セッションにログイン情報がない場合はログイン画面にリダイレクト
    if (!isset($_SESSION['id'])) {
        header('Location: login.php');
        exit();
    }
    // 自分のID
    $user1_id = $_SESSION['id'];
    // メッセージを変数に格納
    $output = "";
    // チャットルームのIDが指定されている場合
    if (isset($_GET['room_id'])) {
        $room_id = $_GET['room_id'];
        try {
            // 対象の user2_id を userrooms テーブルから取得
            $stmt = $pdo->prepare("
                SELECT user1_id, user2_id
                FROM userrooms
                WHERE room_id = ?
            ");
            $stmt->execute([$room_id]);
            $userroom = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$userroom) {
                throw new Exception("指定されたチャットルームが見つかりません。");
            }
    
            // 自分が user1 なら相手は user2、それ以外なら user1
            $user2_id = ($userroom['user1_id'] == $user1_id) ? $userroom['user2_id'] : $userroom['user1_id'];
    
            // コメントとチャットを取得（投稿も含む）
            $stmt = $pdo->prepare("
                SELECT * FROM (

                SELECT 
                    c.comment_date AS date,
                    c.comment_detail AS detail,
                    -- 投稿内容を取得
                    p.post_detail AS post_detail,
                    c.user_id,
                    -- コメントかチャットかを判別するための type カラム
                    'comment' AS type
                FROM comments c
                -- comments テーブルと posts テーブルを post_id をキーに結合します。
                -- これにより、コメントの対象となる投稿の情報も取得できます。
                JOIN posts p ON c.post_id = p.post_id
                -- 自分が user1 で相手が user2 の場合、またはその逆の場合のコメントを取得
                WHERE (c.user_id = :user1_id AND p.user_id = :user2_id)
                   OR (c.user_id = :user2_id AND p.user_id = :user1_id)
                
                UNION ALL
                
                SELECT 
                    ch.talk_date AS date,
                    ch.talk_detail AS detail,
                    -- チャットには投稿の情報がないため、固定値 NULL を設定。
                    NULL AS post_detail,
                    ch.user_id,
                    -- コメントかチャットかを判別するための type カラム
                    'chat' AS type
                FROM chats ch
                WHERE ch.room_id = :room_id
                
                ) AS combined
                ORDER BY date DESC
                LIMIT :limit OFFSET :offset
            ");
            //読み込み件数
            $limit=50;
            //読み込み開始位置
            $offset =0;
            $stmt->bindValue(':user1_id', $user1_id, PDO::PARAM_INT);
            $stmt->bindValue(':user2_id', $user2_id, PDO::PARAM_INT);
            $stmt->bindValue(':room_id', $room_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
    
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['data'])) {
            $data = $_POST['data'];

            try {
                // SQL文を準備
                $sql = "INSERT INTO chats (user_id, room_id, talk_detail, talk_date)
                        VALUES (:user_id, :room_id, :talk_detail, NOW())";
                $stmt = $pdo->prepare($sql); // PDO::prepare を使用
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
                $stmt->bindParam(':talk_detail', $data, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    // 再読み込み
                    header("Location: " . $_SERVER['PHP_SELF'] . "?room_id=" . $room_id);
                    exit();
                } else {
                    echo "<p>メッセージの送信に失敗しました。</p>";
                }
            } catch (PDOException $e) {
                echo "<p>エラー: " . $e->getMessage() . "</p>";
            }
        }
    }
    ?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トーク画面</title>
    <link rel="stylesheet" href="css/talk.css?v=<?php echo filemtime('./js/talk.js'); ?>">



</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
  
    <div class="chat-container">
        <div class="header">
            <a href="talkitiran.php" class="back-button">←戻る</a>
            <span class="user">匿名A</span>
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
