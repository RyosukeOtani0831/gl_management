<html>
  <head>
    <title>配信専用ページ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script type="text/javascript" src="js/jquery-3.7.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>


    <style>
        /* input[type="file"]用のスタイルを調整 */
        .custom-file-input {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            /* font-size: 1rem; */
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>

  </head>
  <body class="login_body">

    <div class="login_bg">
      <!-- ログアウトボタン -->
      <a href="{{ route('test_delivery_logout') }}" class="btn btn-secondary logout-btn">ログアウト</a>
      
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
      
      <form role="form" class="form-inline login_delivery_form input-group" enctype="multipart/form-data" method="post" action="{{ action('TestDeliveryController@deliveryMessage')}}">
        @csrf
        <div class="mb-3 input_style">
          <label for="toGroup" class="form-label">配信するグループ</label>
          <label for="toGroup" class="form-label">※配信アカウントが参加している公開グループに対して一斉配信できます。</label>
          <select class="form-control input-lg" name="roomId" required>
            @foreach($groupList as $group)
            @php
            $userGar = array();
            foreach($group['users'] as $userG){
                      if($userG['isAdmin']){
                          array_push($userGar, $userG['userId']);
                      }
                  }
              @endphp
              @if($group['public'] && array_search($oita_delivery_user_id, $userGar) !== false)
                  <option value="{{$group['id']}}">{{$group['name']}}</option>
              @endif
          @endforeach
          </select>
        </div>

        <div class="mb-3 input_style">
          <label for="InputConetents" class="form-label">投稿内容</label>
          <textarea name="text" class="form-control deilvery_textarea" required></textarea>
        </div>

        <div class="mb-3 input_style" style="margin-bottom:0">
          <label for="file-upload" class="form-label">ファイルを選択:</label>
          <input type="file" class="custom-file-input" id="file-upload" name="file-upload[]" multiple>
        </div>
        <ul id="file-list" class="list-group mb-3"></ul>        
        
        <!-- 選択されたファイル名を表示するエリア -->
        <button type="submit" class="btn btn-primary btn-lg btn-block">一斉配信</button>
      </form>
    </div>


<!-- JavaScriptでファイル名をリスト表示 -->
<script>
    document.getElementById('file-upload').addEventListener('change', function(event) {
        const fileList = event.target.files; // 選択されたファイルのリスト
        const fileListDisplay = document.getElementById('file-list');
        
        // リストをリセット
        fileListDisplay.innerHTML = '';

        // 各ファイルの名前をリストに追加
        for (let i = 0; i < fileList.length; i++) {
            const listItem = document.createElement('li');
            listItem.textContent = fileList[i].name;
            listItem.classList.add('list-group-item');
            fileListDisplay.appendChild(listItem);
        }
    });
</script>


  </body>
</html>