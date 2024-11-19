<?php
    session_start();
    require "./php/dbConnect.php";
    // セッションにログイン情報がない場合はログイン画面にリダイレクト
    if (!isset($_SESSION['id'])) {
        header('Location: login.php');
        exit();
    }
    $user_id = $_SESSION['id'];
    // $host = 'localhost'; // ホスト名
    // $username = 'lifeguard_user'; // ユーザー名
    // $password = 'Liguardfe712'; // パスワード
    // $dbname = 'lifeguard'; // データベース名

    // // データベースに接続
    // $connection = new mysqli($host, $username, $password, $dbname);

    // // 接続エラーチェック
    // if ($connection->connect_error) {
    //     die("接続失敗: " . $connection->connect_error);
    // }
    // チャットルームIDの取得
    if (isset($_GET['room_id'])) {
        $room_id = $_GET['room_id'];
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
                // チャットの取得処理
                try {
                    $sqlchat = "SELECT talk_detail, user_id FROM chats WHERE room_id = ?";
                    $stmt = $pdo->prepare($sqlchat); // PDO::prepare を使用
                    $stmt->execute([$room_id]); // バインドされた値を渡して実行

                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // データを配列で取得
                    
                    if ($results) {
                        foreach ($results as $row) {
                            // 各メッセージの取得
                            $messageClass = ((int)$row['user_id'] === (int)$user_id) ? 'right' : 'left';
                            echo "<div class='message $messageClass'>" . htmlspecialchars($row['talk_detail']) . "</div>";
                        }
                    } else {
                        echo "<div class='message'>結果が見つかりませんでした。</div>";
                    }
                } catch (PDOException $e) {
                    echo "<p>エラー: " . $e->getMessage() . "</p>";
                }

            //     $sqlchat = "SELECT talk_detail,user_id FROM chats WHERE room_id = ?";
            //     $stmt = $connection->prepare($sqlchat);
            //     $stmt->bind_param("s", $room_id);

            //     $stmt->execute();
            //     $result = $stmt->get_result();

        
            //     if ($result->num_rows > 0) {
            //         // データを配列として取得
            //         while($row = $result->fetch_assoc()) {
            //                 // 各メッセージの取得
            //                 $messageClass = ((int)$row['user_id'] === (int)$user_id) ? 'right' : 'left';
            //                 echo "<div class='message $messageClass'>" . htmlspecialchars($row['talk_detail']) . "</div>";
            //             }
            //     } else {
            //             echo "<div class='message'>結果が見つかりませんでした。</div>";
                    
            //             // foreach ($row as $key => $value) {
            //             //     if($value[1]=="2"){
            //             //         echo "<div class='message left'>".$value[0] . "</div>";
            //             //         } else{
            //             //         echo "<div class='message right'>".$value[0] . "</div>";
            //             //     }
            //             //     // var_dump($value);中身
            //             // }
            //     }
            // // <div class="message left">ああ</div>
            // // <div class="message right">うう</div>
            ?>
        </div>
        <form action="" method="POST" class="input-area">
            <input type="text" name="data" placeholder="メッセージを入力・・・" required>
            <button type="submit" class="send-btn">送信</button>
            <?php
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
                // if ($_SERVER["REQUEST_METHOD"] == "POST") {
                //     if (isset($_POST['data'])) {


                //         $data = $_POST['data'];
                    
                //         // SQL文を準備
                //         $sql ="INSERT INTO chats (user_id, room_id, talk_detail, talk_date)
                //                 VALUES (1, 1, ?, NOW());";
                //         $stmt = $connection->prepare($sql);
                //         $stmt->bind_param("s", $data);
                    
                //         if ($stmt->execute()) {
                //             header("Location: " . $_SERVER['PHP_SELF'] . "?room_id=" . $room_id);
                //             exit();
                //         } else {
                //             echo "<p>エラー: " . $stmt->error . "</p>";
                //         }
                //     }
                // }
                
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
        // $connection->close();
    ?>
</body>
</html>
