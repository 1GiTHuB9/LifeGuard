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

        <a href="#" class="back-button" onclick="goBack()">←戻る</a>

        <div class="header">
            <h5>ここに診断をしてもらうための説明文を配置</h5><br>
            
        </div>
        
        <form action="diagnosisresult.php" class="questions">
            <div class="question">
                <h5>Q1.犬派ですか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-1" name="Q1" value="1">
                    <label for="Q1-1" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-2" name="Q1" value="2">
                    <label for="Q1-2" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-3" name="Q1" value="3">
                    <label for="Q1-3" class="radio-button-label">3</label>
                </div>
            </div>

            <div class="question">
                <h5>Q2.朝ごはん食べますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-1" name="Q2" value="1">
                    <label for="Q2-1" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-2" name="Q2" value="2">
                    <label for="Q2-2" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-3" name="Q2" value="3">
                    <label for="Q2-3" class="radio-button-label">3</label>
                </div>
            </div>
            <div class="question">
                <h5>Q2.朝ごはん食べますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q3-1" name="Q3" value="1">
                    <label for="Q3-1" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q3-2" name="Q3" value="2">
                    <label for="Q3-2" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q3-3" name="Q3" value="3">
                    <label for="Q3-3" class="radio-button-label">3</label>
                </div>
            </div>
            <div class="question">
                <h5>Q2.朝ごはん食べますか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="option1" name="Q4" value="1">
                    <label for="option1" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="option2" name="Q4" value="2">
                    <label for="option2" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="option3" name="Q4" value="3">
                    <label for="option3" class="radio-button-label">3</label>
                </div>
            </div>



            <br>
            <button type="submit">診断する</button>
        </form>
        </div>





    </div>
    <script>
        function goBack() {
            history.back();
        }
    </script>
</body>
</html>