<?php 
    //セッションハイジャック対策
    session_start();
    session_regenerate_id(true);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>ろくまる農園</title>
</head>
<body>
<?php 

try{//データベース障害対応
require_once('../common/common.php');//関数集に読み込み

$post=sanitize($_POST);//安全対策

$onamae=$post['onamae'];
$email=$post['email'];
$postal1=$post['postal1'];
$postal2=$post['postal2'];
$address=$post['address'];
$tel=$post['tel'];

print $onamae.'様<br>';
print 'ご注文ありがとうございました。<br>';
print $email.'にメールを送りましたのでご確認ください。<br>';
print '商品は以下の住所に発送させていただきます。<br>';
print $postal1.'-'.$postal2.'<br>';
print $address.'<br>';
print $tel.'<br>';

$honbun='';
$honbun.=$onamae."様\n\nこの度はご注文ありがとうございました。\n";
$honbun.="\n";
$honbun.="ご注文商品 \n";
$honbun.="-----------------\n";

$cart=$_SESSION['cart'];
$kazu=$_SESSION['kazu'];
$max=count($cart);

$dsn='mysql:dbname=shop_test00;host=localhost;charset=utf8';
$user='root';
$password='';
$dbh=new PDO($dsn,$user,$password);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

for($i=0; $i<$max; $i++){
    $sql='SELECT name,price FROM mst_product WHERE code=?';
    $stmt=$dbh->prepare($sql);
    $data[0]=$cart[$i];
    $stmt->execute($data);

    $rec=$stmt->fetch(PDO::FETCH_ASSOC);

    $name = $rec['name'];
    $price = $rec['price'];
    $kakaku[] = $price;//DB mst_productから取得した価格を保存した$priceを＄kakakuに保存。 再度 mst_productから読みに行く必要がなくなる
    $suryo = $kazu[$i];
    $shokei = $price * $suryo;

    $honbun.=$name.'';
    $honbun.=$price.'円 × ';
    $honbun.=$suryo.'個 = ';
    $honbun.=$shokei."円 \n";
}

////他の人のアクセスを自分のやり取りが終わるまでロック
$sql='LOCK TABLES dat_sales WRITE,dat_sales_product WRITE';
//prepareメソッドでSQLをセット
$stmt=$dbh->prepare($sql);
//executeでクエリを実行
$stmt->execute();

$sql='INSERT INTO dat_sales(code_member,name,email,postal1,postal2,address,tel) VALUES(?,?,?,?,?,?,?)';
$stmt=$dbh->prepare($sql);
$data=array();//配列変数にすでに入っているデータをクリア
$data[]=0;
$data[]=$onamae;
$data[]=$email;
$data[]=$postal1;
$data[]=$postal2;
$data[]=$address;
$data[]=$tel;
$stmt->execute($data);

//$sql文 AUTO＿INCREMENTで最も最近に発番された番号を取得できる
$sql='SELECT LAST_INSERT_ID()';
//prepareメソッドでSQLをセット
$stmt=$dbh->prepare($sql);
//executeでクエリを実行
$stmt->execute();
$rec=$stmt->fetch(PDO::FETCH_ASSOC);
$lastcode=$rec['LAST_INSERT_ID()'];

for($i=0; $i<$max; $i++){
    $sql='INSERT INTO dat_sales_product(code_sales,code_product,price,quantity) VALUES(?,?,?,?)';
    $stmt=$dbh->prepare($sql);
    $data=array();
    $data[]=$lastcode;
    $data[]=$cart[$i];
    $data[]=$kakaku[$i];
    $data[]=$kazu[$i];
    $stmt->execute($data);
}

//ロック解除。次の人の処理が行われる
$sql='UNLOCK TABLES';
$stmt=$dbh->prepare($sql);
$stmt->execute();

$dbh=null;

$honbun.="送料は無料です。 \n";
$honbun.="----------------\n";
$honbun.="\n";
$honbun.="代金は以下の口座にお振込ください。\n";
$honbun.="ろくまる銀行 やさい支店 普通口座 1234567\n";
$honbun.="入金確認が取れ次第、梱包、発送させていただきます。\n";
$honbun.="\n";
$honbun.="□□□□□□□□□□□□□□□□□□□□□□□□□□\n";
$honbun.="〜安心の野菜ろくまる農園〜\n";
$honbun.="\n";
$honbun.="〇〇県六丸群六丸村123-4\n";
$honbun.="電話 090-6060-××××\n";
$honbun.="メール info@rokumarunouen.co.jp\n";
$honbun.="□□□□□□□□□□□□□□□□□□□□□□□□□□\n";

//print '<br>';
//print nl2br($honbun); →＼nを<br>に変換してブラウザに表示してくれる

//お客様へのメール
$title='ご注文ありがとうございます。';
$header='From：info@rokumarunouen.co.jp';//お店のメールアドレス
$honbun=html_entity_decode($honbun, ENT_QUOTES,'UTF-8');
mb_language('japanese');
mb_internal_encoding('UTF-8');
mb_send_mail($email,$title,$honbun,$header);//ここの$emailはお客様のアドレス

//お店宛のメール
$title='お客様からご注文がありました。';
$header='From：'.$email;
$honbun=html_entity_decode($honbun, ENT_QUOTES,'UTF-8');
mb_language('japanese');
mb_internal_encoding('UTF-8');
mb_send_mail('info@rokumarunouen.co.jp', $title, $honbun, $header);

}catch(Exception $e){
    print 'ただ今障害により大変ご迷惑をおかけしております。';
    exit();
}

?>
<br>
<a href="shop_list.php">商品画面へ</a>
</body>
</html>