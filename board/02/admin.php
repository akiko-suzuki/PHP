<?php

//管理者ページのログインパスワード  define(定数名, 値 [, 大文字と小文字の区別]);
define( 'PASSWORD', 'adminPassword' );

//データベースの接続情報。定数として宣言
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'board');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$now_date = null;
$data = null;
$file_handle = null;
$split_data = null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();//「表示名」と「ひと言メッセージ」の2つ以上入る可能性があるため、配列形式
$clean = array();

// 掲示板で入力された「表示名」を保存し、掲示板によく書き込みを行う方の入力を少なく
session_start();

if( !empty($_POST['btn_submit']) ) {

    //パスワードの未入力チェックと正しいパスワードが入力されているかを確認
    if( !empty($_POST['admin_password']) && $_POST['admin_password'] === PASSWORD ){
        //いずれも満たしている場合にのみ、ログインセッション$_SESSION[‘admin_login’]を作成して値にtrueを設定
        $_SESSION['admin_login'] = true;
    }else{
        $error_message[] = 'ログインに失敗しました。';
    }
}

//　データベースに接続
$mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

//　接続エラーの確認
if( $mysql->connect_errno ){
    $error_message[] = 'データの読み込みに失敗しました。エラー番号 '.$mysql->connect_errno.' : '.$mysql->connect_error;
}else{
    
    //今回は全データを取得するため指定WHERE句は指定しない。ORDER BY句は、特定のカラムの値で取得データを並び替える。降順にデータを取得する「DESC」を指定
    $sql = "SELECT id,view_name,message,post_date FROM message ORDER BY post_date DESC";
    // $sqlを、次のqueryメソッドで実行。返り値はmysqli_resultクラスのオブジェクトが$resに入る
    $res = $mysql->query($sql);

    //mysqli_resultクラスのオブジェクトが取得できていることを確認し、fetch_allメソッドで取得したデータを全て取得
    if( $res ){
        //今回はデータをファイル読み込みのときと同様の配列形式で取得したいため、メソッドに「MYSQLI_ASSOC」を指定。
        //これで$message_arrayに連想配列の形式でデータを取得することができる
        $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }
    //切断
    $mysql->close();
}

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
<!-- 変数$error_messageは配列形式になっているため、foreach文を使って配列の値の数だけメッセージを表示するようにする -->
<?php if( !empty($error_message) ): ?>
    <ul class="error_message">
        <!-- $error_messageから1つずつ値を取り出して$valueに代入し、そのままli要素の中にecho関数で出力 -->
        <?php foreach( $error_message as $value ): ?>
            <li>・<?php echo $value; ?></li>
        <?php endforeach; ?>
	</ul>
<?php endif; ?>

<section>
<?php if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ): ?>
<form method="get" action="./download.php">
    <!-- 「ダウンロード」ボタンが押されると「limit」と名前のついたselect要素も一緒に「download.php」へPOST送信されるようになる -->
    <select name="limit">
        <option value="">全て</option>
        <option value="10">10件</option>
        <option value="30">30件</option>
    </select>
    <input type="submit" name="btn_download" value="ダウンロード">
</form>
<!-- $message_arrayが空じゃないかチェック -->
<?php if( !empty($message_array) ){ ?>
<!-- foreach文で$message_arrayからメッセージ1件分のデータを取り出し、$valueに挿入 -->
<?php foreach( $message_array as $value ){ ?>
<!-- 表示名、投稿日時、メッセージ内容の3つをそれぞれecho関数で出力 -->
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <!-- 文字列形式になっている時間をstrtotime関数でタイムスタンプ形式に変換,その後、date関数で時刻フォーマット「‘Y年m月d日 H:i’」の形で時刻を取得し、出力 -->
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