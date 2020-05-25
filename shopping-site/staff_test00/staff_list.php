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
try {

//データベース接続<-----
$dsn='mysql:dbname=shop_test00;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh=new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//----->

//スタッフの名前とを全部ちょうだいと言うSQL文
$sql='SELECT code,name FROM mst_staff WHERE 1';
$stmt=$dbh->prepare($sql);
//この命令が終わった時点で、$stmtのな中には、全てのデータが入ってる。execute→意味:実行する//「->」アロー演算子
$stmt->execute();

$dbh=null;//データベースから切断

print 'スタッフ一覧<br><br>';

print'<form method="post" action="staff_branch.php">';
while(true){//<-----スタッフのお名前を$stmtから1レコードづつ取り出しながら表示
    //それをfetch、一行取り出して$recという箱に入れろ
    $rec=$stmt->fetch(PDO::FETCH_ASSOC);
    ////取り出すデータが無くなったらループから脱出
    if($rec==false){
        break;
    }
    //1行ずつ取り出し出したデータを1つずつ表示させる
    print'<input type="radio" name="staffcode" value="'.$rec['code'].'">';
    print $rec['name'];
    print '<br>';
}//----->
print'<input type="submit" name="disp" value="参照">';
print'<input type="submit" name="add" value="追加">';
print'<input type="submit" name="edit" value="修正">';
print'<input type="submit" name="delete" value="削除">';
print'</form>';

}catch (Exception $e){
    print 'ただいま障害により大変ご迷惑をおかけしております。';
    exit();
}

?>
<br>
<a href="staff_login/staff_top.php">トップメニューへ</a>
</body>
</html>