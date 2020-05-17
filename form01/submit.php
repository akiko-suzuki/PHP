<?php 

session_start();

//サーバーに保存されているsessionデータを変数に代入
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$message = $_SESSION['message'];

?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>DB(データーベース)に接続していないフォーム|送信画面</title>
<link rel="stylesheet" href="css/form.css">
</head>
<body>
<div class="container">
<h1>完了しました。</h1>
<p class="thanks">お問い合わせありがとうございました。</p>
<p class="to-top"><a href="input.php">フォームTop</a></p>

<?php 
// メール本文の文字コード設定.mb=メールを送る際の設定
mb_language("Japanese");
mb_internal_encoding("UTF-8");

$to = "{$email}";//入力されて変数に代入された$emilを＄toに代入。メールの宛先
$title = "【お問い合わせメールフォームより】";//件名【】は有っても無くてもok

//差出人の設定
$headers = "From:".mb_encode_mimeheader("株式会社〇〇〇〇〇");//差出人、会社名
$headers.="\n";
$headers.="Bcc: xxxxx@email";//管理者のメールにBCCで送信

//EMO内がメールの中身として送られる。-----も含まれる。
$body =  <<<EOM
    ------------------------------------------------------
    【お問い合せ内容の確認】

    お名前：{$name}
    メールアドレス：{$email}
    お問い合わせ内容：{$message}


    {$name}様、お問い合わせ、誠にありがとうございました。
     後ほど、担当の者よりご連絡いたしますので、お待ちください。

    -------------------------------------------------------
EOM;

// メール送信の実行
//mb_send_mailでメールを送る事が出来る
$rc = mb_send_mail($to, $title, $body, $headers);

if (!$rc) {
    //$rcがひとつでもなかったら送らない
     exit;
} else {
    $_SESSION = NULL;
}
//最後にセッション情報を破棄
session_destroy();

?>

</div>
</body>
</html>