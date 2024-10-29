<?php
// DB接続のための設定
require "./php/db.php"; // db.php のパスを確認してください

// データベースから顔文字を取得する関数
function getReactions($conn) {
    $sql = "SELECT reaction, reaction_date FROM calendars WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':user_id', 1, PDO::PARAM_INT); // user_idを1に固定
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 顔文字を取得
$reactions = getReactions($conn);
$user_id = 1; // 一時的にuser_idを1に固定
$reactions = getReactions($conn, $user_id); // データベースから顔文字を取得

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    //$user_id = 1; // 一時的にuser_idを1に固定
    $reaction = $_POST['reaction'];
    $reaction_date = $_POST['reaction_date'];

    $sql = "INSERT INTO calendars (user_id, reaction, reaction_date) VALUES (:user_id, :reaction, :reaction_date)";
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':reaction', $reaction, PDO::PARAM_STR);
        $stmt->bindValue(':reaction_date', $reaction_date, PDO::PARAM_STR);
        $stmt->execute();

        echo "レコードが正常に保存されました。";
    } catch (PDOException $e) {
        error_log($e->getMessage()); // エラーログに記録
        echo "エラーが発生しました。";
    }

    $conn = null;
    exit;
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

    <div class="calendar-container">
        <button id="prev-month" class="month-nav-btn">◀</button>
        <div class="month-header" id="month-header"></div>
        <div class="calendar" id="calendar"></div>
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
let reactions = <?php echo json_encode($reactions); ?>; // PHPから顔文字のデータをJavaScriptに渡す
renderCalendar(currentDate);

prevMonthBtn.onclick = () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    slideCalendar(-1);
};

nextMonthBtn.onclick = () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    slideCalendar(1);
};

function renderCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const firstDayOfMonth = new Date(year, month, 1).getDay();
    const monthNames = ["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"];
    const dayNames = ["日", "月", "火", "水", "木", "金", "土"];

    // 反応に対する顔文字画像のマッピング
    const reactionToImageMap = {
        "にっこり": "nicokan.png",
        "泣き": "nakikan.png",
        "怒り": "okokan.png"
    };

    monthHeader.textContent = `${year}年 ${monthNames[month]}`;
    calendar.innerHTML = '';

    dayNames.forEach(day => {
        const headerElement = document.createElement('div');
        headerElement.className = 'header';
        headerElement.textContent = day;
        calendar.appendChild(headerElement);
    });

    for (let i = 0; i < firstDayOfMonth; i++) {
        const emptyCell = document.createElement('div');
        calendar.appendChild(emptyCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'day';
        dayElement.innerHTML = `<strong>${day}</strong> <div class="kaomoji" id="kaomoji-${day}"></div>`;
        dayElement.onclick = () => showKaomojiSelector(dayElement);

        // データベースからの反応を表示
        const reactionForDate = reactions.find(r => {
            const reactionDate = new Date(r.reaction_date);
            return reactionDate.getDate() === day && reactionDate.getMonth() === month && reactionDate.getFullYear() === year;
        });
        if (reactionForDate) {
            const kaomojiImage = reactionToImageMap[reactionForDate.reaction]; // マッピングから画像を取得
            if (kaomojiImage) {
                const reactionImage = document.createElement('img');
                reactionImage.src = `./img/${kaomojiImage}`; // 画像のパスを正確に指定
                reactionImage.alt = reactionForDate.reaction;
                dayElement.querySelector('.kaomoji').appendChild(reactionImage);
            }
        }

        calendar.appendChild(dayElement);
    }
}

function slideCalendar(direction) {
    const initialPosition = direction === 1 ? 'slide-right' : 'slide-left';
    calendar.classList.add(initialPosition);

    setTimeout(() => {
        renderCalendar(currentDate);
        calendar.classList.remove(initialPosition);
    }, 500);
}

const kaomojiModal = document.getElementById('kaomoji-modal');
const overlay = document.getElementById('overlay');
const kaomojiOptions = document.querySelectorAll('.kaomoji-option');
const confirmBtn = document.getElementById('confirm-btn');
const cancelBtn = document.getElementById('cancel-btn');

let selectedDayElement = null;
let selectedReaction = null;

kaomojiOptions.forEach(option => {
    option.onclick = () => {
        selectedReaction = option.getAttribute('data-kaomoji');
        kaomojiOptions.forEach(opt => opt.classList.remove('selected'));
        option.classList.add('selected');
    };
});

function showKaomojiSelector(dayElement) {
    selectedDayElement = dayElement;
    overlay.style.display = 'block';
    kaomojiModal.style.display = 'block';
}

function closeKaomojiSelector() {
    overlay.style.display = 'none';
    kaomojiModal.style.display = 'none';
}

const reactionMap = {
    "nicokan.png": "にっこり",
    "nakikan.png": "泣き",
    "okokan.png": "怒り"
};

confirmBtn.onclick = () => {
    if (selectedReaction && selectedDayElement) {
        const kaomojiDiv = selectedDayElement.querySelector('.kaomoji');
        kaomojiDiv.innerHTML = `<img src="./img/${selectedReaction}" alt="Kaomoji">`;
        closeKaomojiSelector();

        const reaction = reactionMap[selectedReaction]; // ここで反応を取得
        if (!reaction) {
            alert("無効な顔文字です。");
            return;
        }
        const userId = 1; // 一時的にuser_idを1に固定
        const day = selectedDayElement.querySelector('strong').textContent;
        const reactionDate = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(selectedDayElement.querySelector('strong').textContent).padStart(2, '0')}`;

        saveReaction(userId, reaction, reactionDate); // reactionをここで使用
    } else {
        alert("顔文字を選択してください。");
    }
};

function saveReaction(userId, reaction, reactionDate) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "", true); // 空のURLで同じファイルに送信
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log("保存結果:", xhr.responseText);
        }
    };
    xhr.send(`user_id=${userId}&reaction=${reaction}&reaction_date=${reactionDate}`);
}
cancelBtn.onclick = closeKaomojiSelector;
overlay.onclick = closeKaomojiSelector;
</script>

</body>
</html>
