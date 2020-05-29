<?php

define( 'PASSWORD', 'adminPassword' );

$dsn='mysql:dbname=board;host=localhost;charset=utf8';
$user='root';
$password='';

date_default_timezone_set('Asia/Tokyo');

$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();

session_start();

if( !empty($_GET['btn_logout']) ) {
    unset($_SESSION['admin_login']);
}

if( !empty($_POST['btn_submit']) ) {

    if( !empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD ){
        $_SESSION['admin_login'] = true;
    }else{
        $error_message[] = 'ログインに失敗しました。';
    }
}

try {
    $dbh=new PDO($dsn,$user,$password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT id,view_name,message,post_date FROM message ORDER BY post_date DESC";
    $stmt=$dbh->prepare($sql);
    $stmt->execute();
    if( $stmt ){
        $message_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}catch (Exception $e){
    $error_message[] = 'データの読み込みに失敗しました。エラー：'.$e->getMessage();
}
$dbh=null;//ここの位置でいいのかしら

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ひと言掲示板 管理ページ</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>ひと言掲示板　管理ページ</h1>
<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
        <?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
	</ul>
<?php endif; ?>

<section>
<?php if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ): ?>
<form method="get" action="./download.php">
    <select name="limit">
        <option value="">全て</option>
        <option value="10">10件</option>
        <option value="30">30件</option>
    </select>
    <input type="submit" name="btn_download" value="ダウンロード">
</form>
<?php if( !empty($message_array) ){ ?>
<?php foreach( $message_array as $value ){ ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
        <p class="edit_delete_btn">
            <a href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>
            &nbsp;
            <a href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a>
        </p>
    </div>
    <p><?php echo nl2br($value['message']); ?></p>
</article>
<?php } ?>
<?php } ?>
<form method="get" action="">
    <input type="submit" name="btn_logout" value="ログアウト">
</form>
<?php else: ?>
<form method="post">
    <div>
        <label for="admin_password">ログインパスワード</label>
        <input type="password" name="admin_password" id="admin_password" value="">
    </div>
    <input type="submit" name="btn_submit" value="ログイン">
</form>
<?php endif; ?>
</section>

</body>
</html>