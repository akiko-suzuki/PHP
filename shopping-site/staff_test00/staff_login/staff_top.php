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
<body>ショップ管理トップメニュー<br>
<br>
<a href="../../staff_test00/staff_list.php">スタッフ管理</a><br>
<br>
<a href="../../product_test00/pro_list.php">商品管理</a><br>
<br>
<a href="../../order_test00/order_download.php">注文ダウンロード</a><br>
<br>
<a href="staff_logout.php">ログアウト</a>
</body>
</html>