<?php
//////// 運営者しかアクセスできないページ ////////

$dsn='mysql:dbname=board;host=localhost;charset=utf8';
$user='root';
$password='';

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

try {
    if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ) {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=メッセージデータ.csv");
        header("Content-Transfer-Encoding: binary");

        $dbh=new PDO($dsn,$user,$password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        if( !empty($limit) ) {
            $sql = "SELECT * FROM message ORDER BY post_date ASC LIMIT $limit";
        } else {
            $sql = "SELECT * FROM message ORDER BY post_date ASC";
        }
        $stmt=$dbh->prepare($sql);
        $stmt->execute();

        if( $stmt ){
        $message_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $dbh=null;

    }

    if( !empty($message_array) ) {
        $csv_data .= '"ID","表示名","メッセージ","投稿日時"'."\n";

        foreach( $message_array as $value ) {
            $csv_data .= '"' . $value['id'] . '","' . $value['view_name'] . '","' . $value['message'] . '","' . $value['post_date'] . "\"\n";
        }

        echo $csv_data;
    }

}catch ( Exception $e ) {
    header("Location: ./admin.php");
}

return;//どこにリターンするんだろう

?>