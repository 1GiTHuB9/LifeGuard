<?php
// MySQLの接続設定
$servername = "localhost";
$username = "lifeguard_user";  // MySQLユーザー名
$password = "Liguardfe712";      // MySQLパスワード
$dbname = "lifeguard";  // データベース名

$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーチェック
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// POSTリクエストからデータを取得
$date = $_POST['date'];
$reaction = $_POST['reaction'];

// 反応を保存または更新するSQL文
$sql = "INSERT INTO calendar (user_id,reaction_date, reaction) VALUES (1,?, ?)
        ON DUPLICATE KEY UPDATE reaction = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $date, $reaction, $reaction);

if ($stmt->execute()) {
    echo "反応が保存されました";
} else {
    echo "エラー: " . $conn->error;
}

// MySQL接続を閉じる
$conn->close();
?>