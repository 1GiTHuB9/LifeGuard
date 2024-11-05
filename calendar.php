<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カレンダー</title>
    <link rel="stylesheet" href="./css/calendar.css?<?php echo date('YmdHis'); ?>">
</head>
<body>
    <div>
        <a href="javascript:history.back()" class="back-link">⇐戻る</a>
    </div>
    <div class="fullscreen-image">
        <img src="./img/haikei4 1.png" alt="Full Screen Image">
    </div>

    <!-- タイトルテキスト -->
    <div class="title-container">
        <h1 class="title-text">今日の調子を登録しよう！</h1>
    </div>

    <div class="calendar-container">
        <div class="calendar-header">
            <button id="prev-month" onclick="changeMonth(-1)">&#8249;</button>
            <h2 id="current-month-year"></h2>
            <button id="next-month" onclick="changeMonth(1)">&#8250;</button>
        </div>
        <div id="calendar"></div>
    </div>

    <!-- 反応を選択するモーダル -->
    <div id="reaction-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>反応を選択してください:</h3>
            <button onclick="selectReaction('&#x1f625;')"><h1>&#x1f60a;</h1></button>
            <button onclick="selectReaction('&#x1f600;')"><h1>&#x1f600;</h1></button>
            <button onclick="selectReaction('&#x1f630;')"><h1>&#x1f630;</h1></button>
            <button onclick="selectReaction('&#x1f625;')"><h1>&#x1f625;</h1></button>
            <button onclick="selectReaction('&#x1f621;')"><h1>&#x1f621;</h1></button>
        </div>
    </div>

    <!-- 外部JavaScriptファイルを読み込む -->
    <script src="./js/calendar.js"></script>

</body>
</html>