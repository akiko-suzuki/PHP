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

}elseif( !empty($_POST['message_id']) ) {

    // $message_idに整数型にサニタイズした値を代入
    $message_id = (int)htmlspecialchars($_POST['message_id'],ENT_QUOTES);

    // データベースに接続
    $mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

    //　接続エラーの確認
    if( $mysql->connect_errno ){
        $error_message[] = 'データベースの読み込みに失敗しました。エラー番号 '.$mysql->connect_errno.' : '.$mysql->connect_error;
        }else{
            //データの削除　削除したいデータを特定するために「テーブル名」とWHERE句を指定
            $sql = "DELETE FROM message WHERE id = $message_id";
            // 作成したSQLをqueryメソッドに渡して実行
            $res = $mysql->query($sql);
        }

        $mysql->close();

        if( $res ) {
            header("Location: ./admin.php");
        }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ひと言掲示板 管理ページ(投稿の削除)</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>ひと言掲示板 管理ページ(投稿の削除)</h1>

<!-- 変数$error_messageは配列形式になっているため、foreach文を使って配列の値の数だけメッセージを表示するようにする -->
<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
        <!-- $error_messageから1つずつ値を取り出して$valueに代入し、そのままli要素の中にecho関数で出力 -->
        <?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
	</ul>
<?php endif; ?>
<p class="text-confirm">以下の投稿を削除します。<br>よろしければ「削除」ボタンを押してください。</p>
<form method="post">
	<div>
        <!-- for属性は、ラベル付け対象のフォームid属性値を指定 -->
        <label for="view_name">表示名</label>
        <!-- disabled属性はフォームを入力できないようにするための属性で、今回のように内容を確認することが目的になる場合に使用 -->
		<input id="view_name" type="text" name="view_name" value="<?php if( !empty($message_data['view_name']) ) { echo $message_data['view_name']; }  ?>"　disabled>
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message" disabled><?php if( !empty($message_data['message']) ) { echo $message_data['message']; } ?></textarea>
    </div>
    <a class="btn_cancel" href="admin.php">キャンセル</a>
    <input type="submit" name="btn_submit" value="削除">
    <input type="hidden" name="message_id" value="<?php echo $message_data['id']; ?>">
</form>

</body>
</html>