<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Eメール登録時の注意事項 - MediLine Workplace</title>
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center py-8">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-2xl mx-4">
        <!-- タイトル -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">MediLine Workplace</h1>
            <p class="text-xl text-gray-600">Eメール登録時の注意事項</p>
        </div>

        <!-- 注意事項コンテンツ -->
        <div class="space-y-6 text-gray-700">
            <!-- キャリアメールアドレスの場合 -->
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                <h2 class="text-lg font-bold text-gray-800 mb-3">
                    ＜キャリアメールアドレスを登録する場合＞
                </h2>
                <div class="space-y-2">
                    <p class="text-red-600 font-semibold">
                        ・「docomo」のキャリアメールは使用しないでください。
                    </p>
                    <p class="text-red-600 pl-4">
                        迷惑メール対策で登録用メールが受信できません。(設定回避不能)
                    </p>
                    <p class="mt-3">
                        ・<span class="text-blue-600 font-semibold">「au、SoftBank」</span>など各キャリアメールの場合、
                    </p>
                    <p class="pl-4">
                        迷惑メールの対策などで、メールが受信できない場合がございます。
                    </p>
                    <p class="pl-4 mt-2">
                        事前に「<span class="text-red-600 font-semibold">@mediline.jp</span>」を受信できるように設定してください。
                    </p>
                    <p class="pl-4">
                        設定方法は、各キャリアのWEBサイトをご覧ください。
                    </p>
                </div>
            </div>

            <!-- 通常のメールアドレスの場合 -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <h2 class="text-lg font-bold text-gray-800 mb-3">
                    ＜通常のメールアドレスを登録する場合＞
                </h2>
                <div class="space-y-2">
                    <p>
                        お使いのメールソフト、ウィルス対策ソフト等の設定により
                    </p>
                    <p>
                        「迷惑メール」に振り分けられる場合があります。
                    </p>
                    <p class="mt-2">
                        受信後に、「<span class="text-red-600 font-semibold">@mediline.jp</span>」を迷惑メールの対象から除外設定してください。
                    </p>
                </div>
            </div>

            <!-- 戻るボタン -->
            <div class="text-center mt-8">
                <button onclick="window.close()" 
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded focus:outline-none focus:shadow-outline transition duration-200">
                    閉じる
                </button>
            </div>
        </div>
    </div>

    <!-- レスポンシブ対応のスタイル -->
    <style>
        @media (max-width: 640px) {
            .max-w-2xl {
                max-width: 100%;
                margin: 1rem;
            }
        }
    </style>
</body>
</html>