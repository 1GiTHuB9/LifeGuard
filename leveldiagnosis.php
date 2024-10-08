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
                    <label for="Q1-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-2" name="Q1" value="2">
                    <label for="Q1-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-3" name="Q1" value="3">
                    <label for="Q1-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-3" name="Q1" value="3">
                    <label for="Q1-3" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q1-3" name="Q1" value="3">
                    <label for="Q1-3" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q1.犬派ですか？</h5>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-1" name="Q2" value="0">
                    <label for="Q1-1" class="radio-button-label">0</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-2" name="Q2" value="1">
                    <label for="Q1-2" class="radio-button-label">1</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-3" name="Q2" value="2">
                    <label for="Q1-3" class="radio-button-label">2</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-4" name="Q2" value="3">
                    <label for="Q1-3" class="radio-button-label">3</label>
                </div>
                <div class="radio-button-wrapper">
                    <input type="radio" id="Q2-5" name="Q2" value="4">
                    <label for="Q1-3" class="radio-button-label">4</label>
                </div>
            </div>
            <div class="question">
                <h5>Q1.犬派ですか？</h5>
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
                    <input type="radio" id="Q3-3" name="Q3" value="4">
                    <label for="Q3-5" class="radio-button-label">4</label>
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
<!-- レベル診断チェックリスト

1:あなたは最近、気分が持続的に沈んでいると感じていますか？
2:疲れや倦怠感を感じることがありますか？
3:食欲が減退していると感じますか？
4:睡眠の質が悪くなっていると感じますか？
5:集中力や注意力が低下していると感じますか？
6:仕事や日常生活に対する興味を持てなくなっていますか？
7:自分に対する評価が低くなっていると感じますか？
8:他人との交流を避けるようになっていますか？
9:自分の未来に対して希望を持てなくなっていますか？
10:無価値感や罪悪感を感じることがありますか？

それぞれ5段階で評価します（横の数字はポイントです。これの合計でレベルを判断します。

0: 全く感じない
1: あまり感じない。
2: 時々感じる。
3: よく感じる。
4: ほぼ常に感じる。

5段階で評価します。

レベル1：０～５: 今のところ問題ありません。
レベル2：６～10: 治療を要するレベルではありませんが、医療機関に相談してみてもよいかもしれません。
レベル3：11～15: 不安な状態が長引くようでしたら医療機関にご相談ください。
レベル4：16～20: 早い時期に専門医に診てもらうことをお勧めします。
レベル5：21～27: 速やかに専門医に診てもらってください。 -->
