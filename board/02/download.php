<?php
//////// 運営者しかアクセスできないページ ////////

// データベースの接続情報。定数として宣言
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'board');

// 変数の初期化
$csv_data = null;
$sql = null;
$res = null;
$message_array = array();
$limit = null;

session_start();

// 取得件数　　GETパラメータのlimitが送信されていたら値をセットするように設定
if( !empty($_GET['limit']) ) {
    if( $_GET['limit'] === "10" ) {
        $limit = 10;
    } elseif( $_GET['limit'] === "30" ) {
        $limit = 30;
    }
}
 // パスワードの未入力チェックと正しいパスワードが入力されているかを確認
if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ) {
    // 出力の設定 「Content-Type」「ファイル名」「エンコーディング」を順に指定
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=メッセージデータ.csv");
    header("Content-Transfer-Encoding: binary");

    // データベースに接続
    $mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

    // 接続エラーの確認
    if( !$mysql->connect_errno ) {

        //empty関数で$limitに値が入っているかを確認し、もし値がセットされていたらLIMIT句をsqlに指定
        if( !empty($limit) ) {
            // //今回は全データを取得するため指定WHERE句は指定しない。ORDER BY句は、特定のカラムの値で取得データを並び替える。昇順(登録された順)にデータを取得する「ASC」を指定。LIMITで $limitに入ってる数を取得
            $sql = "SELECT * FROM message ORDER BY post_date ASC LIMIT $limit";
        } else {
            $sql = "SELECT * FROM message ORDER BY post_date ASC";
        }

        // $sqlを、次のqueryメソッドで実行。返り値はmysqli_resultクラスのオブジェクトが$resに入る
        $res = $mysql->query($sql);

        //今回はデータをファイル読み込みのときと同様の配列形式で取得したいため、メソッドに「MYSQLI_ASSOC」を指定。
        //これで$message_arrayに連想配列の形式でデータを取得することができる
        if( $res ) {
            $message_array = $res->fetch_all(MYSQLI_ASSOC);
        }

        // 切断
        $mysql->close();
    }

    // CSVデータを作成  ファイルとして出力する投稿データがあるかを確認
    if( !empty($message_array) ) {
        //1行目のラベル作成  もし投稿データがあれば、CSVファイルの1行目を生成
        $csv_data .= '"ID","表示名","メッセージ","投稿日時"'."\n";

        // $error_messageから投稿データを1つずつ値を取り出して$valueに代入しファールを作っていく
        foreach( $message_array as $value ) {

            // データを1行ずつCSVファイルに書き込む　$csv_dataにファイルの内容を追記していく
            $csv_data .= '"' . $value['id'] . '","' . $value['view_name'] . '","' . $value['message'] . '","' . $value['post_date'] . "\"\n";
        }

        // ファイルを出力
        echo $csv_data;
    }
}else{
    //ログインページリダイレクト　もし運営者以外のユーザーによるアクセスだった場合は、header関数を使って「admin.php」に移動
    header("Location: ./admin.php");
}

// 「download.php」はページを表示しないため明示的にreturn;を記述
return;











?>