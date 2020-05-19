<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title>データーベースに接続していないフォーム｜入力画面</title>
<link rel="stylesheet" href="css/form.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
<div class="container">
<h1>データーベースに接続していない<br><i class="fa fa-envelope-open-o" aria-hidden="true"></i>お問い合わせフォーム</h1>
<div class="form-main">
  <div class="form-wrapper">
        <form class="form" action="confirm.php" method="post">
            <p class="name">
                <input class="feedback-input" type="text" name="name" required placeholder="name">
            </p>      
            <p class="email">
                <input class="feedback-input" type="email" name="email" required placeholder="Email">
            </p>
            <p class="text">
                <textarea class="feedback-input" placeholder="Message" name="message" required></textarea>
            </p>
            <div class="submit">
                <input type="submit" value="確認画面へ" class="btn-submit">
            </div>
        </form>
  </div>
</div>
</div><!-- .container -->
</body>
</html>