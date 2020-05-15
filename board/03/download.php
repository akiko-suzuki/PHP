<?php
//////// 運営者しかアクセスできないページ ////////

define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'board');

$csv_data = null;
$sql = null;
$res = null;
$message_array = array();
$limit = null;

session_start();

if( !empty($_GET['limit']) ) {
    if( $_GET['limit'] === "10" ) {
        $limit = 10;
    } elseif( $_GET['limit'] === "30" ) {
        $limit = 30;
    }
}

if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ) {
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=メッセージデータ.csv");
    header("Content-Transfer-Encoding: binary");

    $mysql = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

    if( !$mysql->connect_errno ) {

        if( !empty($limit) ) {
            $sql = "SELECT * FROM message ORDER BY post_date ASC LIMIT $limit";
        } else {
            $sql = "SELECT * FROM message ORDER BY post_date ASC";
        }

        $res = $mysql->query($sql);

        if( $res ) {
            $message_array = $res->fetch_all(MYSQLI_ASSOC);
        }

        $mysql->close();
    }

    if( !empty($message_array) ) {
        $csv_data .= '"ID","表示名","メッセージ","投稿日時"'."\n";

        foreach( $message_array as $value ) {
            $csv_data .= '"' . $value['id'] . '","' . $value['view_name'] . '","' . $value['message'] . '","' . $value['post_date'] . "\"\n";
        }

        echo $csv_data;
    }
}else{
    header("Location: ./admin.php");
}

return;

?>