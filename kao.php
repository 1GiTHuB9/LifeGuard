<?php
// DB接続のための設定を含む
require "./php/dbConnect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POSTデータを取得
    $calendar_id = $_POST['calendar_id'];
    $user_id = $_POST['user_id'];
    $reaction = $_POST['reaction']; // VARCHARなのでそのまま
    $reaction_date = $_POST['reaction_date']; // DATE型

    // SQL文を準備して実行
    $sql = "INSERT INTO calendars (calendar_id, user_id, reaction, reaction_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $calendar_id, $user_id, $reaction, $reaction_date); // VARCHARにはsを使用

    if ($stmt->execute()) {
        echo "Record saved successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // 接続を閉じる
    $stmt->close();
    $conn->close();
    exit; // POSTリクエスト処理後はスクリプトを終了
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaomoji Calendar</title>
    <link rel="stylesheet" href="./css/kao.css">
</head>
<body>
<div class="fullscreen-image">
    <img src="haikei4.png" alt="Full Screen Image">

    <!-- カレンダーのコンテナ -->
    <div class="calendar-container">
        <!-- 左矢印（前月へ） -->
        <button id="prev-month" class="month-nav-btn">◀</button>

        <!-- 月名を表示 -->
        <div class="month-header" id="month-header"></div>

        <!-- カレンダー -->
        <div class="calendar" id="calendar"></div>

        <!-- 右矢印（次月へ） -->
        <button id="next-month" class="month-nav-btn">▶</button>
    </div>

    <!-- 顔文字選択モーダル -->
    <div class="overlay" id="overlay"></div>
    <div id="kaomoji-modal" class="kaomoji-selector">
        <p>顔文字を選択してください:</p>
        <div id="kaomoji-list">
            <img src="./img/nicokan.png" alt="Kaomoji 1" data-kaomoji="nicokan.png" class="kaomoji-option">
            <img src="./img/nakikan.png" alt="Kaomoji 2" data-kaomoji="nakikan.png" class="kaomoji-option">
            <img src="./img/okokan.png" alt="Kaomoji 3" data-kaomoji="okokan.png" class="kaomoji-option">
        </div>
        <br><br>
        <button id="confirm-btn">決定</button>
        <button id="cancel-btn">キャンセル</button>
    </div>
</div>
<script>
const monthHeader = document.getElementById('month-header');
const calendar = document.getElementById('calendar');
const prevMonthBtn = document.getElementById('prev-month');
const nextMonthBtn = document.getElementById('next-month');

let currentDate = new Date();
renderCalendar(currentDate);

// 前月ボタンのクリックイベント
prevMonthBtn.onclick = () => {
    currentDate.setMonth(currentDate.getMonth() - 1); // 月を減少
    slideCalendar(-1); // 左にスライド
};

// 次月ボタンのクリックイベント
nextMonthBtn.onclick = () => {
    currentDate.setMonth(currentDate.getMonth() + 1); // 月を増加
    slideCalendar(1); // 右にスライド
};

// カレンダーを描画する関数
function renderCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate(); // 月の日数を取得
    const firstDayOfMonth = new Date(year, month, 1).getDay(); // 月の初日の曜日

    const monthNames = ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"];
    const dayNames = ["日", "月", "火", "水", "木", "金", "土"];

    // 月と年のヘッダーを表示
    monthHeader.textContent = `${year}年 ${monthNames[month]}`;

    // カレンダーのクリア
    calendar.innerHTML = '';

    // 曜日ヘッダーを追加
    dayNames.forEach(day => {
        const headerElement = document.createElement('div');
        headerElement.className = 'header';
        headerElement.textContent = day;
        calendar.appendChild(headerElement);
    });

    // 空のセルを最初に追加して、月初日の曜日位置を調整
    for (let i = 0; i < firstDayOfMonth; i++) {
        const emptyCell = document.createElement('div');
        calendar.appendChild(emptyCell);
    }

    // カレンダーの日付を生成
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'day';
        dayElement.innerHTML = `<strong>${day}</strong> <div class="kaomoji" id="kaomoji-${day}"></div>`;
        dayElement.onclick = () => showKaomojiSelector(dayElement);
        calendar.appendChild(dayElement);
    }
}

// カレンダーをスライドするアニメーション関数
function slideCalendar(direction) {
    const initialPosition = direction === 1 ? 'slide-right' : 'slide-left';
    calendar.classList.add(initialPosition);

    setTimeout(() => {
        renderCalendar(currentDate);
        calendar.classList.remove(initialPosition);
    }, 500); // アニメーションの時間と同期させる
}

// モーダルに関連するコード
const kaomojiModal = document.getElementById('kaomoji-modal');
const overlay = document.getElementById('overlay');
const kaomojiOptions = document.querySelectorAll('.kaomoji-option');
const confirmBtn = document.getElementById('confirm-btn');
const cancelBtn = document.getElementById('cancel-btn');

let selectedDayElement = null;
let selectedReaction = null;

// 顔文字をクリックで選択
kaomojiOptions.forEach(option => {
    option.onclick = () => {
        selectedReaction = option.getAttribute('data-kaomoji'); // 選択された顔文字を保存
        kaomojiOptions.forEach(opt => opt.classList.remove('selected')); // 他の選択を解除
        option.classList.add('selected'); // 選択をハイライト
    };
});

function showKaomojiSelector(dayElement) {
    selectedDayElement = dayElement; // 選択された日付要素を保存
    overlay.style.display = 'block';
    kaomojiModal.style.display = 'block';
}

function closeKaomojiSelector() {
    overlay.style.display = 'none';
    kaomojiModal.style.display = 'none';
}

// 決定ボタンのクリックイベント
confirmBtn.onclick = () => {
    if (selectedReaction && selectedDayElement) {
        const kaomojiDiv = selectedDayElement.querySelector('.kaomoji');
        kaomojiDiv.innerHTML = `<img src="./img/${selectedReaction}" alt="Kaomoji">`;
        closeKaomojiSelector();

        // 選択された顔文字を reaction の値にマップ
        const reactionMap = {
            "nicokan.png": 1,
            "nakikan.png": 2,
            "okokan.png": 3
        };
        const reaction = reactionMap[selectedReaction];
        const calendarId = 1; // カレンダーのロジックに応じて調整
        const userId = 123; // 実際のユーザーIDに置き換える
        const reactionDate = `${currentDate.getFullYear()}-${currentDate.getMonth() + 1}-${selectedDayElement.querySelector('strong').textContent}`;

        saveReaction(calendarId, userId, reaction, reactionDate);
    } else {
        alert("顔文字を選択してください。"); // 絵文字が選択されていない場合のアラート
    }
};

function saveReaction(calendarId, userId, reaction, reactionDate) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "", true); // 同じページにPOST
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log("Reaction saved successfully:", xhr.responseText);
        }
    };
    xhr.send(`calendar_id=${calendarId}&user_id=${userId}&reaction=${reaction}&reaction_date=${reactionDate}`);
}
cancelBtn.onclick = closeKaomojiSelector;
overlay.onclick = closeKaomojiSelector;
</script>

</body>
</html>
