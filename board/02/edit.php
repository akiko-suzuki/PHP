<?php
//////// 運営者しかアクセスできないページ ////////

//データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'board');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期設定
$message_id = null;
$mysql = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array(); 

session_start();

//　管理者としてログインしてるか確認
if( empty($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true ) {
    // ログインページへリダイレクト
    header("Location: ./admin.php");
}

// GETパラメータで投稿IDである「message_id」が渡されていたら、if文の中で投稿IDに該当する投稿（メッセージ）をデータベースから取得。
if( !empty($_GET['message_id']) && empty($_POST['message_id']) ) {
    // GETパラメータで渡された投稿IDをサニタイズして、$message_idに代入
    $message_id = (int)htmlspecialchars($_GET['message_id'], ENT_QUOTES);

    // データベースに接続
    $mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

    //　接続エラーの確認
    if( $mysql->connect_errno ){
    $error_message[] = 'データベースの読み込みに失敗しました。エラー番号 '.$mysql->connect_errno.' : '.$mysql->connect_error;
    }else{
        //データの読み込み
        $sql = "SELECT * FROM message WHERE id = $message_id";
        $res = $mysql->query($sql);

        if( $res ) {
            // 該当する投稿を取得できたら、連想配列形式で$message_dataにデータを代入
            $message_data = $res->fetch_assoc();
        }else{
            // データが読み込めなかったら一覧に戻る
            header("Location: ./admin.php");
        }

        $mysql->close();
    }

}elseif( !empty($_POST['message']) ) {

    // $message_idに整数型にサニタイズした値を代入
    $message_id = (int)htmlspecialchars($_POST['message_id'],ENT_QUOTES);

    // empty関数で値が入っているかを確認し、もし未入力だった場合はエラーメッセージを$error_messageに代入
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

    // $error_messageにエラーメッセージが入っているかを確認して未入力項目があったかを確認
    if( empty($error_message) ) {
        // データベースに接続
        $mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

        //エラーの確認
        if( $mysql->connect_errno ) {
            $error_message[] = 'データベースの接続に失敗しました。エラー番号' . $mysql->connect_errno . ':' . $mysql->connect_errno;
        }else {

            // UPDATE文で更新する「表示名」と「メッセージ」のデータをセット。WHERE句に投稿IDを指定して更新する投稿データを検索する。
            // SET句はデータを更新したいカラムと値をセットで指定します。更新するカラムが複数ある場合は「, (コンマ)」で区切って指定することができる。
            $sql = "UPDATE message set view_name = '$message_data[view_name]', message= '$message_data[message]' WHERE id =  $message_id";
            $res = $mysql->query($sql);
        }

        $mysql->close();

        // 更新に成功したら一覧に戻る
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

<!-- 変数$error_messageは配列形式になっているため、foreach文を使って配列の値の数だけメッセージを表示するようにする -->
<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
        <!-- $error_messageから1つずつ値を取り出して$valueに代入し、そのままli要素の中にecho関数で出力 -->
        <?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
	</ul>
<?php endif; ?>

<form method="post">
	<div>
        <!-- for属性は、ラベル付け対象のフォームid属性値を指定 -->
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