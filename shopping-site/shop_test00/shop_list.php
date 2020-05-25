<?php 
session_start();
session_regenerate_id(true);//合言葉をかえる
if(isset($_SESSION['member_login'])==false){
    print'ようこそゲスト様　';
    print'<a href="member_login.html">会員ログイン</a><br>';
    print '<br>';
}else{
    print'ようこそゲ';
    print $_SESSION['member_name'];
    print '様　<br>';
    print'<a href="member_logout.php">ログアウト</a><br>';
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
try {

//データベース接続<-----
$dsn='mysql:dbname=shop_test00;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh=new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//----->

$sql='SELECT code,name,price FROM mst_product WHERE 1';
$stmt=$dbh->prepare($sql);
//この命令が終わった時点で、$stmtのな中には、全てのデータが入ってる。execute→意味:実行する//「->」アロー演算子
$stmt->execute();

$dbh=null;//データベースから切断

print '商品一覧<br><br>';

while(true){
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    if($rec==false){
        break;
    }

    print '<a href="shop_product.php?procode='.$rec['code'].'">';
	print $rec['name'].'---';
    print $rec['price'].'円';
    print '</a>';
    print '<br>';
}
print '<br>';
print '<a href="shop_cartlook.php">カートを見る</a><br>';

}catch (Exception $e){
    print 'ただいま障害により大変ご迷惑をおかけしております。';
    exit();
}

?>

</body>
</html>