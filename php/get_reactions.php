<?php
// MySQLの接続設定
// （本番環境）
$host = 'mysql-3.mc.lolipop.lan';
$dbname = '3caf596e27571a5610d17d2e8bf5e786';
$username = '3caf596e27571a5610d17d2e8bf5e786';
$password = 'Liguardfe712';
    
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーチェック
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

$sql = "SELECT reaction, reaction_date FROM calendars WHERE reaction_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
if (!$stmt->execute()) {
    die("クエリ実行エラー: " . $conn->error);
}
$result = $stmt->get_result();

$reactions = array();

while($row = $result->fetch_assoc()) {
    $reactions[] = $row;
}

// JSON形式で結果を返す
header('Content-Type: application/json');
echo json_encode($reactions);

$conn->close();
?>