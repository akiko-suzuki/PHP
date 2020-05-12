<?php
// メッセージを保存するファイルのパス設定
define( 'FILENAME', './message.txt');

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

if( !empty($_POST['btn_submit']) ) {

    //表示名の入力チェック
    if( empty($_POST['view_name']) ){
        $error_message[] = '表示名を入力してください。';
    } else {
        // 不正な入力を無くすための入力データを無害化する前処理「サニタイズ」
        // 記号をHTMLエンティティという形式に変換することでコードの無害化を行う。
        $clean['view_name'] = htmlspecialchars($_POST['view_name'],ENT_QUOTES);
        // 入力されたデータの中に改行コード「\r\n」「\n」「\r」をそれぞれ検索し。表示名については空文字に置き換え改行を削除
        $clean['view_name'] = preg_replace('/\\r\\n|\\n|\\r/', '', $clean['view_name']);
    }

    //メッセージの入力チェック
    if( empty($_POST['message']) ){
        $error_message[] = 'ひと言メッセージを入力してください。';
    }else {
        $clean['message'] = htmlspecialchars($_POST['message'],ENT_QUOTES);
        // 改行コード「\r\n」「\n」「\r」をそれぞれ検索し、メッセージは<br>要素へ置き換え
        $clean['message'] = preg_replace('/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
    }
    
        if( empty($error_message) ) {

        /* 
        既存のファイルへの書き込み操作
        if( $file_handle = fopen( FILENAME, "a") ) {

	    // 書き込み日時を取得
		$now_date = date("Y-m-d H:i:s");
	
		// 書き込むデータを作成
		$data = "'".$clean['view_name']."','".$clean['message']."','".$now_date."'\n";
	
		// 書き込み
		fwrite( $file_handle, $data);
	
		// ファイルを閉じる
		fclose( $file_handle);

		$success_message = 'メッセージを書き込みました。';
        }
        */

        //データベースに接続
        $mysql = new mysqli( 'localhost', 'root', '', 'board');
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
            
            if( $res ){
                $success_message = 'メッセージを書き込みました。';
            }else{
                $error_message = '書き込みに失敗しました。';
            }

            //データべース切断
            $mysql->close();
        }
    }		
}

/*
if( $file_handle = fopen( FILENAME,'r') ) {
    //fgetsはファイルから1行ずつデータを取得するための関数(5)
    while( $data = fgets($file_handle) ){

        //preg_splitは文字列を特定の文字で分割する関数(5)
		$split_data = preg_split( '/\'/', $data);
        
		$message = array(
			'view_name' => $split_data[1],//名前
			'message' => $split_data[3],//メッセージ
			'post_date' => $split_data[5]//日付
		);
		array_unshift( $message_array, $message);
	}
    
    // ファイルを閉じる
    fclose( $file_handle);
}
*/

//　データベースに接続
$mysql = new mysqli( 'localhost', 'root', '', 'board');

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

<!-- empty関数で$success_messageに値が入っているかを確認し入っていればecho-->
<?php if( !empty($success_message) ): ?>
    <p class="success_message"><?php echo $success_message; ?></p> 
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
		<input id="view_name" type="text" name="view_name" value="">
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
    <p><?php echo $value['message']; ?></p>
</article>

<?php } ?>
<?php } ?>
</section>
</body>
</html>