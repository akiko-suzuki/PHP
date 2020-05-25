<?php 
session_start();
session_regenerate_id(true);//合言葉をかえる
if(isset($_SESSION['login'])==false){
    print'ログインされていません。<br>';
    print'<a href="../staff_login/staff_login.html">ログイン画面へ</a>';
    exit();
}else{
    print $_SESSION['staff_name'];
    print 'さんログイン中<br>';
    print '<br>';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ろくまる農園</title>
</head>
<body>
<?php 

try{
//データベースが正常に動いているときに動く本来のプログラム
$staff_code=$_POST['code'];

//DBにSQL文で命令する
$dsn='mysql:dbname=shop_test00;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh=new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//すでにあるレコード削除するSQL文
$sql='DELETE FROM mst_staff WHERE code=?';
$stmt=$dbh->prepare($sql);
$data[]=$staff_code;
$stmt->execute($data);

 //DBとのアクセスを切断する
$dbh=null;

//データベースがダウンしているときに動くプログラム
}catch (Exception $e){
    print 'ただいま障害により大変ご迷惑をおかけしております。';
    exit();
}
?>
削除しました。<br>
<br>
<a href="staff_list.php"> 戻る</a>
</body>
</html>