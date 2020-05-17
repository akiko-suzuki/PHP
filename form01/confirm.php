<?php 

//$_POST["name"]の値が空だったら（!=not）Locationで指定しているファイルに強制移動（リダイレクト）させる
if( !(isset($_POST['name'])) ){
    header('Location:input.php');    
    //scriptを終了する
    exit; 
}

$name = htmlspecialchars($_POST["name"], ENT_QUOTES);//要素を文字列と認識する記述
$email = htmlspecialchars($_POST["email"], ENT_QUOTES);
$message = htmlspecialchars($_POST["message"], ENT_QUOTES);

// 入力値をセッション変数に格納
session_start();
$_SESSION['name'] = $name;
$_SESSION['email'] = $email;
$_SESSION['message'] = $message;

?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>DB(データーベース)に接続していないフォーム｜確認画面</title>
<link rel="stylesheet" href="css/form.css">
</head>

<body> 
<div class="container">
<h1>確認画面</h1>
<div class="form-main">
    <div class="form-wrapper">
        <form action="submit.php" method="post">
            <p class="cf-name">name：<?php echo $name; ?></p>
            <p class="cf-email">email：<?php echo $email; ?></p>
            <p class="cf-text">message<br><?php echo $message; ?></p>
            <div class="cf-submit">
                <input type="button" value="戻る" onclick="history.back();" class="btn-submit">
                <input type="submit" value="送信" class="btn-submit">
            </div>
        </form>
    </div>
</div>
</div>
</body>
</html>
