<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>管理画面</title>
</head>

<body>
<?php 
// 接続設定
$user = '';//DBユーザー名
$pass = '';//BDパスワード
$dsn = '';

$dbh = new PDO($dsn, $user, $pass); 
//上の情報を1つの変数にまとめている//「$conn」は任意のオブジェクト名（変数名）

$dbh -> query('SET NAMES UTF8');

//テーブルの中身を一括で取得する *=全て
$sql = 'SELECT * FROM mytable WHERE 1';//WHERE部分を打ち換えると指定したものが表示される
$stmt = $dbh -> prepare($sql);
$stmt -> execute();

//データがあるだけ全部取得
while(1) {
  $rec = $stmt -> fetch(PDO::FETCH_ASSOC);

  //もうデータがなければ「break」でループから抜ける
  if($rec == false) {
    break;
  }
  //   ↑   while文ここまで   ↑         

	// stmtから取得したデータを表示
  echo $rec['id']      .    '：&nbsp;';
  echo $rec['name'].'：&nbsp;';
  echo $rec['email'].'：&nbsp;';
  echo $rec['gender'].'：&nbsp;';
  echo $rec['message'];
  echo '<br>';

}

 $dbh = null;

?>

</body>
</html>