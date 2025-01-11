<?php
session_start();
require "./php/dbConnect.php";
//セッションIDがない場合はログインページにリダイレクト
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}
// ユーザーIDをセッションから取得
$user_id = $_SESSION['id'];
// すでに診断結果があるか確認
    $sql = "SELECT diagnosis_level FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['diagnosis_level'] > 0) {
        // すでに診断結果がある場合、home.php にリダイレクト
        echo "すでに診断結果があります。";
        header('Location: home.php');
        exit();
    }
$errorMessage = '';//エラーメッセージの変数
// フォームが送信されたかどうか確認
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 各質問の値を取得
    $totalScore = 0;
    $allAnswered = true;  // 全ての質問が回答されているかどうかをチェックするフラグ
    for ($i = 1; $i <= 10; $i++) {
        if (isset($_POST["Q$i"])) {
            $totalScore += (int)$_POST["Q$i"];  // ラジオボタンの値を合計
        } else {
            $allAnswered = false;  // 質問が回答されていない場合
            break;  // 回答されていない質問があればループを終了
        }
    }
    if ($allAnswered) {
        // レベルの判定
        if ($totalScore <= 5) {
            $diagnosis_level = 1;
        } elseif ($totalScore <= 10) {
            $diagnosis_level = 2;
        } elseif ($totalScore <= 15) {
            $diagnosis_level = 3;
        } elseif ($totalScore <= 20) {
            $diagnosis_level = 4;
        } elseif ($totalScore <= 27) {
            $diagnosis_level = 5;
        } else {
            $diagnosis_level = 0;  // 異常な場合
        }
        // データベースに診断結果を保存
        $sql = "UPDATE users SET diagnosis_level = :diagnosis_level WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':diagnosis_level', $diagnosis_level, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            // echo $diagnosis_level,"診断結果の保存に成功しました。";
            // 診断結果の保存に成功した場合、結果ページへリダイレクト
            header('Location: diagnosisresult.php?level=' . $diagnosis_level);
            exit();
        } else {
            $errorMessage = "診断結果の保存に失敗しました。";
        }
    } else {
        // 質問にすべて回答していない場合
        $errorMessage = "すべての質問に回答してください。";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>レベル診断</title>
    <link rel="stylesheet" href="./css/style1.css">
</head>
<body>
    <div class="fullscreen-image">
        <img src="img/haikei4.png" alt="Full Screen Image">


        <div class="container1">

        <!-- <a href="#" class="back-button" onclick="goBack()">←戻る</a> -->

        <div class="header">
            <h5>これはあなたの心がどれくらい疲れているかを診断するためのものです。<br>
                下記を参考に深く考えず直感で回答してください。<br>
                <br>
                0: 全く感じない
                1: あまり感じない。
                2: 時々感じる。
                3: よく感じる。
                4: ほぼ常に感じる。</h5><br>
            
        </div>
        
        <form action="leveldiagnosis.php" class="questions" method="post">
            <div class="question">
                <h5>Q1.あなたは最近、気分が持続的に沈んでいると感じていますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-1" name="Q1" value="0">
                    <label for="Q1-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-2" name="Q1" value="1">
                    <label for="Q1-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-3" name="Q1" value="2">
                    <label for="Q1-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-4" name="Q1" value="3">
                    <label for="Q1-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-5" name="Q1" value="4">
                    <label for="Q1-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q2.疲れや倦怠感を感じることがありますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-1" name="Q2" value="0">
                    <label for="Q2-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-2" name="Q2" value="1">
                    <label for="Q2-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-3" name="Q2" value="2">
                    <label for="Q2-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-4" name="Q2" value="3">
                    <label for="Q2-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-5" name="Q2" value="4">
                    <label for="Q2-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q3.食欲が減退していると感じますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q3-1" name="Q3" value="0">
                    <label for="Q3-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q3-2" name="Q3" value="1">
                    <label for="Q3-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q3-3" name="Q3" value="2">
                    <label for="Q3-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q3-4" name="Q3" value="3">
                    <label for="Q3-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q3-5" name="Q3" value="4">
                    <label for="Q3-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q4.睡眠の質が悪くなっていると感じますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q4-1" name="Q4" value="0">
                    <label for="Q4-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q4-2" name="Q4" value="1">
                    <label for="Q4-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q4-3" name="Q4" value="2">
                    <label for="Q4-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q4-4" name="Q4" value="3">
                    <label for="Q4-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q4-5" name="Q4" value="4">
                    <label for="Q4-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q5.集中力や注意力が低下していると感じますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q5-1" name="Q5" value="0">
                    <label for="Q5-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q5-2" name="Q5" value="1">
                    <label for="Q5-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q5-3" name="Q5" value="2">
                    <label for="Q5-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q5-4" name="Q5" value="3">
                    <label for="Q5-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q5-5" name="Q5" value="4">
                    <label for="Q5-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q6.仕事や日常生活に対する興味を持てなくなっていますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q6-1" name="Q6" value="0">
                    <label for="Q6-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q6-2" name="Q6" value="1">
                    <label for="Q6-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q6-3" name="Q6" value="2">
                    <label for="Q6-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q6-4" name="Q6" value="3">
                    <label for="Q6-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q6-5" name="Q6" value="4">
                    <label for="Q6-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q7.自分に対する評価が低くなっていると感じますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q7-1" name="Q7" value="0">
                    <label for="Q7-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q7-2" name="Q7" value="1">
                    <label for="Q7-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q7-3" name="Q7" value="2">
                    <label for="Q7-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q7-4" name="Q7" value="3">
                    <label for="Q7-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q7-5" name="Q7" value="4">
                    <label for="Q7-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q8.他人との交流を避けるようになっていますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q8-1" name="Q8" value="0">
                    <label for="Q8-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q8-2" name="Q8" value="1">
                    <label for="Q8-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q8-3" name="Q8" value="2">
                    <label for="Q8-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q8-4" name="Q8" value="3">
                    <label for="Q8-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q8-5" name="Q8" value="4">
                    <label for="Q8-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q9.自分の未来に対して希望を持てなくなっていますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q9-1" name="Q9" value="0">
                    <label for="Q9-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q9-2" name="Q9" value="1">
                    <label for="Q9-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q9-3" name="Q9" value="2">
                    <label for="Q9-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q9-4" name="Q9" value="3">
                    <label for="Q9-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q9-5" name="Q9" value="4">
                    <label for="Q9-5" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q10.無価値感や罪悪感を感じることがありますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q10-1" name="Q10" value="0">
                    <label for="Q10-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q10-2" name="Q10" value="1">
                    <label for="Q10-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q10-3" name="Q10" value="2">
                    <label for="Q10-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q10-4" name="Q10" value="3">
                    <label for="Q10-4" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q10-5" name="Q10" value="4">
                    <label for="Q10-5" class="radio-button-label">4</label>
                </div>
            </div>

            <br>
            <button type="submit">診断する</button>
        </form>
        <!-- エラーメッセージの表示 -->
        <?php if ($errorMessage): ?>
            <div class="error-message" style="color: red;">
                <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        </div>

    </div>
    <script>
        // function goBack() {
        //     history.back();
        // }
    </script>
</body>
</html>
