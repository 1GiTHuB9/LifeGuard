<?php
    $host = 'localhost'; // ホスト名
    $username = 'lifeguard_user'; // ユーザー名
    $password = 'Liguardfe712'; // パスワード
    $dbname = 'lifeguard'; // データベース名

    // データベースに接続
    $connection = new mysqli($host, $username, $password, $dbname);

    // 接続エラーチェック
    if ($connection->connect_error) {
        die("接続失敗: " . $connection->connect_error);
    }


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トーク画面</title>
    <link rel="stylesheet" href="css/talk.css">



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
                if (isset($_GET['room_id'])) {
                    $room_id = $_GET['room_id'];
                }


                $sqlchat = "SELECT talk_detail,user_id FROM chats WHERE room_id = ?";
                $stmt = $connection->prepare($sqlchat);
                $stmt->bind_param("s", $room_id);

                $stmt->execute();
                $result = $stmt->get_result();

        
                if ($result->num_rows > 0) {
                    // データを配列として取得
                    while($row = $result->fetch_assoc()) {
                            // 各メッセージの取得
                            $messageClass = ($row['user_id'] != "2") ? 'right' : 'left';
                            echo "<div class='message $messageClass'>" . htmlspecialchars($row['talk_detail']) . "</div>";
                        }
                } else {
                        echo "<div class='message'>結果が見つかりませんでした。</div>";
                    






                        // foreach ($row as $key => $value) {
                        //     if($value[1]=="2"){
                        //         echo "<div class='message left'>".$value[0] . "</div>";
                        //         } else{
                        //         echo "<div class='message right'>".$value[0] . "</div>";
                        //     }
                        //     // var_dump($value);中身
                        // }
                }
            // <div class="message left">ああ</div>
            // <div class="message right">うう</div>
            ?>
        </div>
        <form action="" method="POST" class="input-area">
            <input type="text" name="data" placeholder="メッセージを入力・・・" required>
            <button type="submit" class="send-btn">送信</button>
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST['data'])) {

                        $data = $_POST['data'];
                    
                        // SQL文を準備
                        $sql ="INSERT INTO chats (user_id, room_id, talk_detail, talk_date)
                                VALUES (1, 1, ?, NOW());";
                        $stmt = $connection->prepare($sql);
                        $stmt->bind_param("s", $data);
                    
                        if ($stmt->execute()) {
                            echo "<script>setTimeout(scrollToBottom, 0);</script>";
                        } else {
                            echo "<p>エラー: " . $stmt->error . "</p>";
                        }
                    }
                }
            ?>
        </form>
    </div>
    </div>

    <script>
        function goBack() {
            history.back();
        }    
        function scrollToBottom() {
        const chatWindow = document.querySelector('.chat-window');
        chatWindow.scrollTop = chatWindow.scrollHeight; // 一番下にスクロールする
        }
        window.onload = function() {
            scrollToBottom(); // ページ読み込み時に最下位にスクロール
        }
    </script>
    <?php
        // 接続を閉じる
        $connection->close();
    ?>
</body>
</html>
