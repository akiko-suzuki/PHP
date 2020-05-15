<?php
//////// 運営者しかアクセスできないページ ////////

define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'board');

date_default_timezone_set('Asia/Tokyo');

$message_id = null;
$mysql = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array(); 

session_start();

if( empty($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true ) {
    header("Location: ./admin.php");
}

if( !empty($_GET['message_id']) && empty($_POST['message_id']) ) {
    $message_id = (int)htmlspecialchars($_GET['message_id'], ENT_QUOTES);
    $mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if( $mysql->connect_errno ){
    $error_message[] = 'データベースの読み込みに失敗しました。エラー番号 '.$mysql->connect_errno.' : '.$mysql->connect_error;
    }else{
        $sql = "SELECT * FROM message WHERE id = $message_id";
        $res = $mysql->query($sql);

        if( $res ) {
            $message_data = $res->fetch_assoc();
        }else{
            header("Location: ./admin.php");
        }

        $mysql->close();
    }

}elseif( !empty($_POST['message_id']) ) {
    $message_id = (int)htmlspecialchars($_POST['message_id'],ENT_QUOTES);

    if( empty($_POST['view_name']) ) {
        $error_message[] = '表示名を入力してください。';
    }else{
        $message_data['view_name'] = htmlspecialchars($_POST['view_name'],ENT_QUOTES);
    }

    if( empty($_POST['message']) ) {
        $error_message[] = 'メッセージを入力してください。';
    }else{
        $message_data['message'] = htmlspecialchars($_POST['message'],ENT_QUOTES);
    }

    if( empty($error_message) ) {
        $mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if( $mysql->connect_errno ) {
            $error_message[] = 'データベースの接続に失敗しました。エラー番号' . $mysql->connect_errno . ':' . $mysql->connect_errno;
        }else {
            $sql = "UPDATE message set view_name = '$message_data[view_name]', message= '$message_data[message]' WHERE id =  $message_id";
            $res = $mysql->query($sql);
        }

        $mysql->close();

        if( $res ) {
            header("Location: ./admin.php");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ひと言掲示板 管理ページ(投稿の編集)</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>ひと言掲示板 管理ページ(投稿の編集)</h1>

<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
        <?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
	</ul>
<?php endif; ?>

<form method="post">
	<div>
		<label for="view_name">表示名</label>
		<input id="view_name" type="text" name="view_name" value="<?php if( !empty($message_data['view_name']) ) { echo $message_data['view_name']; }  ?>">
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"><?php if( !empty($message_data['message']) ) { echo $message_data['message']; } ?></textarea>
    </div>
    <a class="btn_cancel" href="admin.php">キャンセル</a>
    <input type="submit" name="btn_submit" value="更新">
    <input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
</form>

</body>
</html>