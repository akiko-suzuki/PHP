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
$pro_name=$_POST['name'];
$pro_price=$_POST['price'];
$pro_gazou=$_FILES['gazou'];//受け取ったファイルの情報を取り出す

$pro_name = htmlspecialchars($pro_name,ENT_QUOTES,'UTF-8');
$pro_price = htmlspecialchars($pro_price,ENT_QUOTES,'UTF-8');

if($pro_name == ''){
    print'商品名が入力されていません。<br>';
}else{
    print '商品名：';
    print $pro_name;
    print '<br>';
}
//データがあるべき形になってないとpreg_matchは0を返す。あるべき形は1。'/¥A[0-9]+¥z/'→半角数字を表している
if(preg_match('/\A[0-9]+\z/',$pro_price)==0){
    print'価格をきちんと入力してください。<br>';
}else{
    print '価格';
    print $pro_price;
    print '円<br>';
}
if($pro_gazou['size'] > 0){
    //もし画像サイズが0より大きければ「画像あり」
    if($pro_gazou['size'] > 1000000){
        print'画像が大きすぎます。';
    }else{
        //画像を[gazou]フォルダにアップロード
        //move_uploaded_file(移動元,移動先)
        //$pro_gazou['tmp_name']→仮にアップロードされている画像本体の場所と名前
        move_uploaded_file($pro_gazou['tmp_name'],'./gazou/'.$pro_gazou['name']);
        //アップロードした画像を表示
        print'<img src="./gazou/'.$pro_gazou['name'].'">';
        print'<br>';
    }
}
if($pro_name=='' || preg_match('/\A[0-9]+\z/',$pro_price)==0 || $pro_gazou['size'] > 1000000){//||→もしくは
    print'<form>';
    print'<input type="button" onclick="history.back()" value="戻る">';
    print'</form>';
}else{
    print '上記の商品を追加します。<br>';
    print'<form method="post" action="pro_add_done.php">';
    print'<input type="hidden" name="name" value="'.$pro_name.'">';
    print'<input type="hidden" name="price" value="'.$pro_price.'">';
    //.$pro_gazou['name'].画像名を次の画像に渡す
    print'<input type="hidden" name="gazou_name" value="'.$pro_gazou['name'].'">';
    print'<br>';
    print'<input type="button" onclick="history.back()" value="戻る">';
    print'<input type="submit" value="OK">';
    print'</form>';
}

?>
</body>
</html>