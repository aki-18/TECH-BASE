<?php
echo "start!<br>";  // デバック用
echo "<hr>";
// DBへの接続
$dsn = "mysql:dbname=tb230493db;host=localhost";  // data source name
$user = "tb-230493";
$password = "swncWmWTVF";
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); // new => データベースの初期化，PDOはクラス  // PHP Data Object

// tableを削除
$sql = 'DROP TABLE tb_5'; // ここにテーブル名入れてね！
$stmt = $pdo -> query($sql);

echo "<hr>";
echo "finish!<br>"; // デバック用
?>