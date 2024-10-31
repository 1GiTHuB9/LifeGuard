<?php
session_start();
session_destroy(); // セッションの破棄
header("Location: login.php"); // ログインページへリダイレクト
exit();
?>
