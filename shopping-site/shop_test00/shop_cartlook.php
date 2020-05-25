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

//issetを使って存在していたらコピーする、存在していなかったらコピーしない
if(isset($_SESSION['cart'])==true){
    $cart=$_SESSION['cart'];
    $kazu=$_SESSION['kazu'];
    $max=count($cart);
}else{
    //存在しなかったら強制的に０を入れる必要がある
    $max=0;
}
//動作テスト、デバックに使用()内の配列の内容全てを、解説付きで画面に表示してくれる
// var_dump($cart);
// exit();

//配列$maxが0だったら下記を表示
if($max==0){
    print 'カートに商品が入っていません。<br>';
    print '<br>';
    print '<a  href="shop_list.php">商品一覧へ戻る</a>';
    exit();
}

$dsn='mysql:dbname=shop_test00;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh=new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

foreach($cart as $key => $val){
    $sql='SELECT code,name,price,gazou FROM mst_product WHERE code=?';
    $stmt=$dbh->prepare($sql);
    $data[0]=$val;//0と明示的に書いたのは、ループが回るたびに1、2、3•･･となってしまわないため
    $stmt->execute($data);

    $rec=$stmt->fetch(PDO::FETCH_ASSOC);

    $pro_name[]=$rec['name'];
    $pro_price[]=$rec['price'];
    if($rec['gazou'] == ''){
        $pro_gazou[] = '';
    }else{
        $pro_gazou[]='<img src="../product_test00/gazou/'.$rec['gazou'].'">';
    }
}
$dbh = null;

}catch(Exception $e){
    print 'ただいま障害により大変大変ご迷惑をおかけしております。';
    exit();
}

?>

カートの中身<br>
<br>
<form method="post" action="kazu_change.php">
<table border="1">
<tr>
    <td>商品</td>
    <td>商品画像</td>
    <td>価格</td>
    <td>数量</td>
    <td>小計</td>
    <td>削除</td>
</tr>
<?php 
for($i=0; $i<$max; $i++){
?>
<tr>
    <td><?php print $pro_name[$i]; ?></td>
    <td><?php print $pro_gazou[$i]; ?></td>
    <td><?php print $pro_price[$i]; ?>円</td>
    <!-- nameには異なる名前をつけなければいけないため、kazu1、kazu2、kazu3、となるようにする -->
    <td><input type="text" name="kazu<?php print $i; ?>" value="<?php print $kazu[$i]; ?>"></td>
    <td>合計<?php print $pro_price[$i] * $kazu[$i]; ?>円</td>
    <!-- nameにはチェックボックスにそれぞれ別の名前をつける -->
    <td><input type="checkbox" name="sakujo<?php print $i; ?>"></td>
</tr>
<?php
    }
?>
</table>
<br>
<input type="hidden" name="max" value="<?php print $max; ?>" >
<input type="submit" value="数量変更"><br>
<input type="button" onclick="history.back()" value="戻る">
</form>
<br>
<a href="shop_form.html">ご購入手続きへすすむ</a><br>
</body>
</html>