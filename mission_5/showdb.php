<?php
echo "start!<br>";  // デバック用
echo "<hr>";
// DBへの接続
$dsn = "mysql:dbname=tb230493db;host=localhost";  // data source name
$user = "tb-230493";
$password = "swncWmWTVF";
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); // new => データベースの初期化，PDOはクラス  // PHP Data Object

// dbにあるtable一覧を表示
$sql = "SHOW TABLES";
$result = $pdo -> query($sql);
foreach ( $result as $row ) {
  echo $row[0];
  echo "<br>";
}
echo "<hr>";

// tableの詳細構成を表示
$sql ='SHOW CREATE TABLE tb_5';
$result = $pdo -> query($sql);
foreach ($result as $row){
    echo $row[1];
}
echo "<hr>";


// 入力したデータレコードの抽出・表示
$sql = 'SELECT * FROM tb_5';
$stmt = $pdo -> query($sql);  // queryはSQL文をデータベースに対して発行する．ここではqueryに$sqlを渡している．// $pdoオブジェクトがアロー演算子を使ってquery関数を利用している．(クラスのインスタンス)->(クラスがもつプロパティやメソッド)
$results = $stmt -> fetchAll();
foreach ( $results as $row ) {
  // $rowの中にはテーブルのカラム名が入る
  echo $row['id'].', ';
  echo $row['name'].', ';
  echo $row['comment'].', ';
  echo $row['dt'].', ';
  echo $row['pass'].'<br>';
}


/*
// 入力したデータレコードの抽出・表示
$id = 1;
$stmt = $pdo -> prepare('SELECT pass FROM tb_5 WHERE id=:id');
//$stmt = $pdo -> prepare($sql);
$stmt -> bindParam(":id", $id, PDO::PARAM_INT);
$stmt -> execute();
$results = $stmt -> fetchAll();
var_dump( $results );
echo "<br>";
echo $results[0][0];
*/

/*
foreach ( $results as $row ) {
  // $rowの中にはテーブルのカラム名が入る
  echo $row['id'].', ';
  echo $row['name'].', ';
  echo $row['comment'].', ';
  echo $row['dt'].', ';
  echo $row['pass'].'<br>';
}
*/

echo "<hr>";
echo "finish!<br>"; // デバック用
?>