<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ユーザー登録 - MediLine Workplace</title>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md mx-4">
        <!-- タイトル -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">MediLine Workplace</h1>
            <p class="text-gray-600">ユーザー登録</p>
        </div>

        <!-- メッセージ表示 -->
        @if (Session::has('message'))
            <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        <!-- エラーメッセージ -->
        @if ($errors->has('exception'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold block">登録に失敗しました。</strong>
                <span class="block mt-2">{{ $errors->first('exception') }}</span>
                <span class="block mt-2 text-sm">原因がご不明の際は support@sharemedical.jp までご連絡ください。</span>
            </div>
        @endif

        <!-- 登録フォーム -->
        <form role="form" method="post" action="{{ action('UserContentsController@registrateUser') }}" class="space-y-6">
            @csrf
            
            <!-- カナ入力 -->
            <div>
                <label for="kana" class="block text-gray-700 text-sm font-bold mb-2">
                    カナ（氏名）
                </label>
                <p class="text-xs text-gray-600 mb-2">※フルネームで全角カナで入力してください</p>
                <input type="text" 
                       name="kana" 
                       id="kana" 
                       placeholder="例. ヤマダ　タロウ"
                       required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 transition duration-200">
            </div>

            <!-- パスワード入力 -->
            <div>
                <label for="InputPassword" class="block text-gray-700 text-sm font-bold mb-2">
                    パスワード
                </label>
                <p class="text-xs text-gray-600 mb-2">※病院から指定された文字列を入力してください</p>
                <input type="password" 
                       name="password" 
                       id="InputPassword" 
                       required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 transition duration-200">
            </div>

            <!-- メールアドレス入力 -->
            <div>
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                    メールアドレス
                </label>
                <a class="text-xs text-red-600 hover:text-red-800 underline block mb-2" 
                   href="{{ route('note') }}" 
                   target="_blank">
                    ※Eメール登録時の注意事項（必ず確認してください）
                </a>
                <input type="email" 
                       name="email" 
                       id="email" 
                       placeholder="例. user@sample.com"
                       required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 transition duration-200">
            </div>

            <!-- コメント欄 -->
            <div>
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                    コメント欄
                </label>
                <input type="text" 
                       name="description" 
                       id="description" 
                       placeholder="例. ◯◯部　◯◯課"
                       required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 transition duration-200">
            </div>

            <!-- 登録ボタン -->
            <button type="submit" 
                    class="w-full bg-green-700 hover:bg-green-800 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200 transform hover:scale-105">
                登録
            </button>
        </form>
    </div>

    <!-- レスポンシブ対応のスタイル -->
    <style>
        @media (max-width: 640px) {
            .max-w-md {
                max-width: 100%;
                margin: 1rem;
            }
        }
    </style>
</body>
</html>