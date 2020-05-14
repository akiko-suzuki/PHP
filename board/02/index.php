<?php

//データベースの接続情報
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
$message_array = array();
$error_message = array();//「表示名」と「ひと言メッセージ」の2つ以上入る可能性があるため、配列形式
$clean = array();

// 掲示板で入力された「表示名」を保存し、掲示板によく書き込みを行う方の入力を少なく
session_start();

if( !empty($_POST['btn_submit']) ) {

    //表示名の入力チェック
    if( empty($_POST['view_name']) ){
        $error_message[] = '表示名を入力してください。';
    } else {
        // 不正な入力を無くすための入力データを無害化する前処理「サニタイズ」
        // 記号をHTMLエンティティという形式に変換することでコードの無害化を行う。
        $clean['view_name'] = htmlspecialchars($_POST['view_name'],ENT_QUOTES);

        //セッションに表示名を保存
        $_SESSION['view_name'] = $clean['view_name'];

        /*
         入力されたデータの中に改行コード「\r\n」「\n」「\r」をそれぞれ検索し。表示名については空文字に置き換え改行を削除
        $clean['view_name'] = preg_replace('/\\r\\n|\\n|\\r/', '', $clean['view_name']);
        (↑↑20に記載あり)
        */
    }

    //メッセージの入力チェック
    if( empty($_POST['message']) ){
        $error_message[] = 'ひと言メッセージを入力してください。';
    }else {
        $clean['message'] = htmlspecialchars($_POST['message'],ENT_QUOTES);

        // 改行コード「\r\n」「\n」「\r」をそれぞれ検索し、メッセージは<br>要素へ置き換え
        // $clean['message'] = preg_replace('/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
        // (↑↑20に記載あり)
    }

        if( empty($error_message) ) {

        //データベースに接続
        $mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

        //　接続エラーの確認
        if( $mysql->connect_errno){
            $error_message[] = '書き込みに失敗しました。エラー番号 '.$mysql->connect_errno.' : '.$mysql->connect_error;
        }else{

            // 文字コード設定
            $mysql->set_charset('utf8');

            // 書き込み日時を取得
            $now_date = date("Y-m-d H:i:s");
            
            // データを登録するSQL作成  「VALUES」の後ろは実際に登録するデータを指定
            $sql = "INSERT INTO message (view_name, message, post_date) VALUES ( '$clean[view_name]', '$clean[message]', '$now_date')";

            // データを登録
            $res = $mysql->query($sql);
            
            if( $res ) {
				$_SESSION['success_message'] = 'メッセージを書き込みました。';
			}else{
				$error_message[] = '書き込みに失敗しました。';
			}

            //データべース切断
            $mysql->close();
        }

        // 「Location:」の後ろにリンクを指定します。今回は自分自身を呼び出すため、「./」
        header('Location: ./');
    }		
}

//　データベースに接続
$mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

//　接続エラーの確認
if( $mysql->connect_errno){
    $error_message[] = 'データの読み込みに失敗しました。エラー番号 '.$mysql->connect_errno.' : '.$mysql->connect_error;
}else{
    
    //今回は全データを取得するため指定WHERE句は指定しない。ORDER BY句は、特定のカラムの値で取得データを並び替える。降順にデータを取得する「DESC」を指定
    $sql = "SELECT view_name, message, post_date FROM message ORDER BY post_date DESC";
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
<title>ひと言掲示板</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>ひと言掲示板</h1>
<!-- empty関数でPOSTパラメータの「書き込む」ボタンが押されていないか、$success_messageに表示する成功メッセージのセッションがあるかを確認し入っていればechoでセッションに入っているメッセージを出力-->
<!-- unset関数で1度表示したメッセージをセッションから削除するために実行 -->
<?php if( empty($_POST['btn_submit']) && !empty($_SESSION['success_message']) ): ?>
    <p class="success_message"><?php echo $_SESSION['success_message']; ?></p>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

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
		<input id="view_name" type="text" name="view_name" value="<?php if( !empty($_SESSION['view_name']) ){ echo $_SESSION['view_name']; } ?>">
	</div>
	<div>
		<label for="message">ひと言メッセージ</label>
		<textarea id="message" name="message"></textarea>
	</div>
	<input type="submit" name="btn_submit" value="書き込む">
</form>

<hr>

<section>
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
    </div>
    <p><?php echo nl2br($value['message']); ?></p>
</article>

<?php } ?>
<?php } ?>
</section>
</body>
</html>