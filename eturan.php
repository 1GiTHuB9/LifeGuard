<?php
session_start();
require "./php/dbConnect.php"; // データベース接続

if(isset($_SESSION['id'])){
}else{
    header('Location: ./login.php');
}


//データ取得
$sql = "SELECT u.user_id,u.user_name,u.diagnosis_level,p.post_id,p.post_detail,p.post_date,p.post_flag FROM posts as p LEFT OUTER JOIN users as u ON p.user_id = u.user_id WHERE u.diagnosis_level = ? ORDER BY post_date ASC";
        
        $stmt = $pdo->prepare($sql); 
        
        $stmt->bindValue(1, $_SESSION['dlevel'], PDO::PARAM_INT);
        // クエリ実行
        $stmt->execute();
        $post = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャット画面</title>
    <link rel="stylesheet" href="./css/eturan.css">
    <style>
        /* 初期スタイル */
        .detail-area {
            position: absolute;
            right: 0;
            top: 0;
            width: 50%;
            height: 100%;
            background-color: white;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);  /* 初期は右に隠す */
            transition: transform 0.5s ease-in-out;  /* スライドアニメーション */
            display: none;  /* 非表示状態 */
        }

        .chat-area {
            width: 100%;  /* 最初は画面全体 */
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
        <div class="container">
            <header class="header">
                <a href="#" class="back-button" onclick="goBack()">←戻る</a>
                <button class="menu-button">&#9776;</button>
            </header>
            <div class="content">
                <!-- メッセージ一覧 -->
                <main class="chat-area" id="chatArea" onclick="resetLayout()">

                    <?php 
                    #投稿表示用ループ（10件のみ）
                    $count = 0;
                    foreach ($post as $row){
                        if ($count >= 10){
                            break;
                        }
                    ?>
                        <div class="message">
                            <div class="profile-pic"></div>
                            <div class="message-content">
                                <p class="username">
                                <!-- 匿名判別 -->
                                    <?php 
                                    if($row['post_flag'] == 1){
                                        echo "匿名";
                                    } else {
                                        echo $row['user_name'];
                                    }
                                    ?>
                                </p>
                                <!-- 投稿内容表示 -->
                                <p class="text" onclick="showDetail('<?php echo $row['post_id'] ?>','<?php echo $row['user_name']?>','<?php echo $row['post_detail']?>'); event.stopPropagation();">
                                    <?php
                                    echo $row['post_detail'];
                                    ?>
                                </p>
                            </div>
                        </div>
                    <?php 
                    $count++;
                    } 
                    ?>
                </main>

                <!-- 詳細エリア（最初は非表示） -->
                <aside class="detail-area" id="detailArea">
                    <div class="comments-section">
                        <!-- 最初のコメント -->
                        <div class="comment">
                            <div class="profile-pic"></div>
                            <div class="comment-content">
                                <p class="username">ユーザー名</p>
                                <p class="text">コメント内容ああああああああああああああ</p>
                            </div>
                        </div>
                
                        <!-- 2番目のコメント -->
                        <div class="comment">
                            <div class="profile-pic"></div>
                            <div class="comment-content">
                                <p class="username">ユーザー名</p>
                                <p class="text">コメント内容ああああああああああああああ</p>
                            </div>
                        </div>
                
                        <!-- コメントを追加 -->
                        <button class="comment-button" id="commentButton">コメント追加</button>
                        <!-- 「もっと見る」ボタン -->
                        <button class="vie-more-button">もっと見る</button>
                    </div>
                </aside>
                
            </div>
            <footer class="footer">
                <button class="add-button" onclick="goToNextPage()">+</button>
            </footer>
        </div>
    </div>

    <script>
        function showDetail(id,name,content) {
            // 詳細エリアをスライドインで表示
            const detailArea = document.getElementById("detailArea");
            detailArea.style.display = "block";  // 詳細エリアを表示
            detailArea.style.transform = "translateX(0)";  // スライドイン

            // 詳細内容を挿入
            detailArea.innerHTML = `
                <div class="comments-section">
                    <div class="comment">
                        <div class="profile-pic"></div>
                        <div class="comment-content">
                            <p class="username">${name}</p>
                            <p class="text">${content}</p>
                        </div>
                    </div>
                    <button class="comment-button" id="dynamicCommentButton">コメント追加</button>
                    <div class="commenter">
                        <div class="profile-commenter"></div>
                        <div class="comment-content">
                            <p class="username">ユーザー名</p>
                            <p class="text">コメント内容:${content}</p>
                        </div>
                    </div>

                    <button class="view-more-button">もっと見る</button>
                </div>
            `;

            // 動的に生成されたボタンにクリックイベントを追加
            document.getElementById("dynamicCommentButton").addEventListener("click", function() {
                goToNextcommet();  // ページ遷移関数を呼び出す
            });

            // コンテンツ全体を2分割レイアウトに変更
            document.getElementById("chatArea").style.width = "50%";
        }
        

        
        function goToNextPage() {
            window.location.href = "consul.php";
        }

        function goToNextcommet() {
            window.location.href = "commentnyuryoku.html";
        }

        function resetLayout() {
            const detailArea = document.getElementById("detailArea");
            const chatArea = document.getElementById("chatArea");

            // 詳細エリアをスライドアウトして非表示にする
            detailArea.style.transform = "translateX(100%)";  // スライドアウト
            setTimeout(() => {
                detailArea.style.display = "none";  // 完全に非表示
            }, 500);  // アニメーション時間と同じだけ待つ

            // チャットエリアの幅を元に戻す
            chatArea.style.width = "100%";
        }

        function goBack() {
            history.back();
        }

        // JavaScriptで固定のコメント追加ボタンがクリックされたときにページ遷移
        document.getElementById("commentButton").addEventListener("click", function() {
            goToNextcommet();  // ページ遷移関数を呼び出す
        });

    </script>
</body>
</html>
