<html>
  <head>
    <title>ログイン</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script type="text/javascript" src="js/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
  </head>
  <body class="login_body">

    <div class="login_bg">
      <div class="login_title">
        <img class="app_logo" src="../assets/logo.svg" style="width:50px;"/>
        <strong>外来Law</strong>
        <h1>配信専用ページ</h1>
      </div>

      <div class="login_message">
      @if (Session::has('message'))
          <strong><p>{{ session('message') }}</p></strong>
      @endif
      @if ($errors->has('exception'))
          <div class="notification">
              <strong><p class="text-danger">{{ $errors->first('exception') }}</strong>
          </div>
      @endif
      </div>

      <form role="form" class="form-inline login_form input-group" method="post" action="{{ action('DeliveryController@loginDeliveryCheck')}}">
      @csrf
        <div class="mb-3 input_style">
          <label for="InputEmail" class="form-label">メールアドレス</label>
          <input type="email" name="email" class="form-control input-lg"" id="InputEmail" aria-describedby="emailHelp" placeholder="Eg. user@example.com" required>
        </div>

        <div class="mb-3 input_style">
          <label for="InputPassword" class="form-label">パスワード</label>
          <input type="password" name="password" class="form-control input-lg"" id="InputPassword" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">次へ</button>
      </form>
    </div>
  </body>
</html>