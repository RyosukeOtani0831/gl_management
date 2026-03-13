<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MediLine Workplace 管理画面ログイン</title>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Arial', 'Helvetica', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
{{-- Google reCAPTCHA v2 --}}
{{-- @if(!config('services.recaptcha.skip', false))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif --}}
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <!-- 背景オーバーレイ削除 -->
    
    <!-- ログインカード -->
    <div class="relative z-10 bg-white rounded-lg shadow-2xl p-8 w-full max-w-md mx-4">
        <!-- タイトル -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">MediLine Workplace</h1>
            <p class="text-gray-600">管理画面ログイン</p>
        </div>

        <!-- エラーメッセージ -->
        @if(!empty($msg))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ $msg }}</span>
            </div>
        @endif
        <!-- ログインフォーム -->
        <form role="form" method="post" action="{{ route('loginCheck') }}" class="space-y-6">
            @csrf
            
            <!-- メールアドレス入力 -->
            <div>
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                    メールアドレス
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500 transition duration-200">
            </div>

            {{-- reCAPTCHA --}}
            {{-- @if(!config('services.recaptcha.skip', false))
                <div class="flex justify-center">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div>
            @endif --}}
            <!-- ログインボタン -->
            <button type="submit" 
                    class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200 transform hover:scale-105">
                ログイン
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