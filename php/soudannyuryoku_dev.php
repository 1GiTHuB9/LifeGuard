<?php
session_start();
require 'dbConnect.php';

function post_func($id,$detail,$anonymous,$pdo){
    // 相談内容投稿処理
    try{
        $sql = "INSERT INTO posts(post_detail,post_date,post_flag,user_id) VALUES(?,?,?,?)";
        $smtp = $pdo->prepare($sql);

        // 値をバインド
        $smtp -> bindValue(1,$detail,PDO::PARAM_STR);
        $smtp -> bindValue(2,date('Y/m/d H:i:s'),PDO::PARAM_STR);
        $smtp -> bindValue(3,$anonymous,PDO::PARAM_STR);
        $smtp -> bindValue(4,$id,PDO::PARAM_STR);

        $smtp -> execute();
    } catch(PDOException $e) {
        // エラーメッセージをキャッチして表示
        echo "データの挿入中にエラーが発生しました: " . $e->getMessage();
        //header('Location:../soudannyuryoku.html');
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POSTデータを取得
    $postStyle = $_POST['postStyle'];
    $content = $_POST['content'];

    //var_dump($content);

    if($postStyle == "公開"){
        $flag = 0;
        post_func($_SESSION['id'],$content,$flag,$pdo);
        echo "投稿完了(公開)";
        header('Location: ../home.html');
        exit;
    } else if($postStyle == "匿名"){
        $flag = 1;
        post_func($_SESSION['id'],$content,$flag,$pdo);
        echo "投稿完了(匿名)";
        header('Location: ../home.html');
        exit;
    } else {
        echo "匿名処理にエラーがあります。";
        exit;
    }
}
?>