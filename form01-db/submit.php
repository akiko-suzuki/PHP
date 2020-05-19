<?php 
session_start();
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$gender = $_SESSION['gender'];
$message = $_SESSION['message'];
//サーバーに保存されているsessionデータを変数に代入
?>
<!--データーベースにデータを送信する-->
<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>DB(データーベース)に接続しているフォーム|送信画面</title>
<link rel="stylesheet" href="css/form.css">
</head>

<body>
<div class="container">
<h1>完了しました。</h1>
<p class="thanks">お問い合わせありがとうございました。</p>
<p class="to-top"><a href="input.php">フォームTop</a></p>

<?php 
// 接続設定
$user = '';//DBユーザー名
$pass = '';//BDパスワード（Macの場合 root）

// XAMPPのデータベースに接続するか確認//記述は変わる
//DB情報-host=サーバー名。dbname=データーベース名
$dsn = '';
$conn = new PDO($dsn, $user, $pass); //上の情報を1つの変数にまとめている//「$conn」は、任意のオブジェクト名（変数名）

// データの追加
$sql = 'INSERT INTO mytable(name, email, gender, message) VALUES("'.$name.'","'.$email.'","'.$gender.'","'.$message.'")';

$stmt = $conn -> prepare($sql);
$stmt -> execute();

//最後にセッション情報を破棄
session_destroy();
?>

</div>
</body>
</html>