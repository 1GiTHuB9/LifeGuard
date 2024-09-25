<?php
// MySQLの接続設定
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lifeguard";

$conn = new mysqli($servername, $username, $password, $dbname);

// 接続エラーチェック
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

$sql = "SELECT * FROM reactions WHERE reaction_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
//$stmt->execute();
if (!$stmt->execute()) {
    die("クエリ実行エラー: " . $conn->error);
}
$result = $stmt->get_result();

$reactions = array();

while($row = $result->fetch_assoc()) {
    $reactions[] = $row;
}

// // 取得したデータをデバッグ
// print_r($reactions);

// // JSON形式で返す
// header('Content-Type: application/json');
// echo json_encode($reactions);
    
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reactions[] = $row;
    }
}

// JSON形式で結果を返す
header('Content-Type: application/json');
echo json_encode($reactions);

$conn->close();
?>