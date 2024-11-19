<?php
session_start();

// ログインチェック
if (!isset($_SESSION['id'])) {
    // ログインしていない場合はログインページにリダイレクト
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム画面</title>
    <link rel="stylesheet" href="./css/home.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="haikei4.png" alt="Full Screen Image">
        <div class="container">
            <button class="menu-button" id="eturan">おすすめの投稿</button>
            <button class="menu-button" id="talk">トーク一覧</button>
            <button class="menu-button" id="chosi">今日の調子？😊</button>
        </div>
        <div class="menu-icon">
            <span>&#9776;</span>
        </div>
        <!-- ハンバーガーメニューの内容 -->
        <div class="side-menu" id="sideMenu">
            <ul>
                <li><a href="usersettei.php">プロフィール</a></li>
                <li><a href="page2.html">レベル診断</a></li>
                <li><a href="#" id="logout">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <script>
        /*document.querySelector('.menu-icon').addEventListener('click', function() {
            document.getElementById('sideMenu').classList.toggle('open');
            event.stopPropagation(); // バブリングを防ぐ
        });*/

        document.querySelector('.menu-icon').addEventListener('click', function() {
        const sideMenu = document.getElementById('sideMenu');
        sideMenu.classList.toggle('open');
        console.log(sideMenu.classList); // クラスの状態を確認
        });

        
    // ハンバーガーメニュー外をクリックしたら閉じる機能
    document.addEventListener('click', function(event) {
        var sideMenu = document.getElementById('sideMenu');
        var menuIcon = document.querySelector('.menu-icon');

        // クリックされた要素がメニューアイコンやメニュー内でなければメニューを閉じる
        if (!sideMenu.contains(event.target) && !menuIcon.contains(event.target)) {
            sideMenu.classList.remove('open');
        }
    });

         // 各ボタンのページ遷移処理
    document.getElementById("talk").addEventListener("click", function() {
        window.location.href = "talkitiran.html";
    });

    document.getElementById("chosi").addEventListener("click", function() {
        window.location.href = "kao.php";
    });

    document.getElementById("eturan").addEventListener("click", function() {
        window.location.href = "eturan.html";
    });

    // ログアウトボタンのクリックイベント
    document.getElementById("logout").addEventListener("click", function(event) {
            event.preventDefault(); // デフォルトのリンク動作を防止
            if (confirm("ログアウトしますか？")) {
                window.location.href = "logout.php"; // ログアウト処理ページへ遷移
            }
        });
    </script>
    
</body>
</html>
