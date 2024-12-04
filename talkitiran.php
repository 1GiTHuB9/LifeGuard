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
    <title>トーク一覧画面</title>
    <link rel="stylesheet" href="css/talkitiran.css">

    


</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
    
        <div class="container">
            <a href="#" class="back-button" onclick="goBack()">←戻る</a>    
            
                <?php

                    $sqlroom = "SELECT room_name,room_id FROM chatrooms";
                    $resultroom = $connection->query($sqlroom);

                    $sqltext = "SELECT c.room_id, c.talk_detail
                                    FROM chats c
                                    JOIN (
                                        SELECT room_id, MAX(message_id) AS max_message_id
                                        FROM chats
                                        GROUP BY room_id
                                    ) AS max_messages ON c.room_id = max_messages.room_id AND c.message_id = max_messages.max_message_id
                                    ORDER BY c.room_id ASC";
                    $resulttext = $connection->query($sqltext);


                    if ($resultroom->num_rows > 0) {
                        while ($row = $resultroom->fetch_assoc()) {
                            $chatrooms[] = $row; // 配列にチャットルーム名を追加
                        }
                        while($row = $resulttext->fetch_assoc()){
                            $texts[] = $row;
                        }
                        
                        if (!empty($chatrooms)) {
                                                    
                            for ($i=0;$i<count($chatrooms);$i++) {
                                echo    "<div class='message'>
                                            <div class='profile-pic'></div>
                                                <div class='message-content' onclick=\"location.href='talk.php?room_id=".htmlspecialchars($chatrooms[$i]['room_id'])."';\">
                                                <p class='username'>".htmlspecialchars($chatrooms[$i]['room_name'])."</p>
                                                <p class='text'>";
                                                if(!empty($texts[$i]['talk_detail'])){
                                                    echo htmlspecialchars($texts[$i]['talk_detail'])."</p>";
                                                }else{
                                                    echo "　　　　　　　　　　</p>";
                                                }
                                            echo "</div>
                                        </div>
                                        <hr class='divider'> <!-- 横棒を追加 -->";
                            }


                        } else {
                            echo "チャットルームがありません";
                        }                   
                    }
                ?>
            
            
        </div>
    </div>
    <script>
        function goBack() {
            history.back();
        }    
    </script>

    <?php
        // 接続を閉じる
        $connection->close();
    ?>
</body>
</html>