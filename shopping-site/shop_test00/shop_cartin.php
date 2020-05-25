<?php 
session_start();
session_regenerate_id(true);//合言葉をかえる
if(isset($_SESSION['member_login'])==false){
    print 'ようこそゲスト様　';
    print '<a href="member_login.html">会員ログイン</a><br>';
    print '<br>';
}else{
    print 'ようこそ';
    print $_SESSION['member_name'];
    print '様　';
    print '<a href="member_logout.php">ログアウト</a><br>';
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
$pro_code=$_GET['procode'];

if(isset($_SESSION['cart'])==true){//もし$_SESSIONにカートが入っていたら
    //現在のカートの内容を$cartにコピーする
    $cart=$_SESSION['cart'];
    $kazu=$_SESSION['kazu'];
    //$cartという配列に存在する$pro_codeを調べる
    if(in_array($pro_code,$cart)==true){
        print 'その商品はすでにカートに入っています。<br />';
		print '<a href="shop_list.php">商品一覧に戻る</a>';
		exit();
    }
}
//カートに商品を追加する
$cart[]=$pro_code;
$kazu[]=1;//数量1
//$_SESSIONにカートを保管する
$_SESSION['cart']=$cart;
$_SESSION['kazu']=$kazu;//後で取り出すように保管


//動作確認に使用した
// foreach($cart as $key => $val){
//     print $val;
//     print '<br>';
// }

}catch(Exception $e){
    print 'ただいま障害により大変大変ご迷惑をおかけしております。';
    exit();
}

?>
カートに追加しました。<br>
<br>
<a href="shop_list.php">商品一覧に戻る</a>

</body>
</html>