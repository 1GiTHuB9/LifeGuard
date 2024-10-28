<?php
session_start();
// セッションにログイン情報がない場合はログイン画面にリダイレクト
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
// URLクエリパラメータから診断レベルを取得
if (isset($_GET['level'])) {
    $diagnosis_level = (int)$_GET['level']; // 整数にキャストして安全に扱う
} else {
    // 診断レベルが渡されていない場合の処理（例: エラーメッセージやデフォルトの表示）
    echo "診断レベルが取得できませんでした。";
    exit();
}

// 診断レベルに応じたメッセージを定義
$resultMessage = '';
switch ($diagnosis_level) {
    case 1:
        $resultMessage = "今のところ問題ありません。";
        break;
    case 2:
        $resultMessage = "治療を要するレベルではありませんが、医療機関に相談してみてもよいかもしれません。";
        break;
    case 3:
        $resultMessage = "不安な状態が長引くようでしたら医療機関にご相談ください。";
        break;
    case 4:
        $resultMessage = "早い時期に専門医に診てもらうことをお勧めします。";
        break;
    case 5:
        $resultMessage = "速やかに専門医に診てもらってください。";
        break;
    default:
        $resultMessage = "診断レベルが不明です。";
        break;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>診断結果</title>
    <link rel="stylesheet" href="./css/style1.css?241023">
</head>
<body>
    <div class="fullscreen-image">
        <img src="img/haikei4.png" alt="Full Screen Image">
      
        <div class="header">
            <h1>診断結果</h1>
            
        </div>

        <div class="diagnosisresult">
            <h5>あなたのレベルは．．．</h5>
            <h1>レベル<?php echo htmlspecialchars($diagnosis_level, ENT_QUOTES, 'UTF-8'); ?>！</h1>
            <!-- 説明文表示 -->
            <div class="result">
            <?php echo htmlspecialchars($resultMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>

        <div class="footer">      
            <a href="home.html" class="button">ホームへ</a><br>
        </div>
    </div>
</body>
</html>