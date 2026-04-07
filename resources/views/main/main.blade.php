<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
    <!-- 現在のheadの内容はそのまま -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="Description" content="外来Law 管理画面" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'green': {
                            700: '#059669',
                            600: '#10b981',
                            50: '#ecfdf5',
                        }
                    }
                }
            }
        }
    </script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <title>外来Law 管理画面</title>
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        <!-- サイドバー -->
        <aside class="w-64 bg-green-700 text-white flex flex-col py-8 px-4 sticky top-0 h-screen overflow-y-auto">
            <h1 class="text-2xl font-bold mb-8">外来Law</h1>
            @include('main/sidebar')
        </aside>

        <!-- メインコンテンツ -->
        <main class="flex-1 p-10" style="overflow-x: auto;">
            @if ($errors->has('exception'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>{{ $errors->first('exception') }}</strong>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong>{{ session('error') }}</strong>
                </div>
            @endif
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <strong>{{ session('success') }}</strong>
                </div>
            @endif

            <div class="tab-content">
                @include('main/teamList')
                
                @if(!$groupPermissionInfo['hideGroupPermission'])
                    {{-- @include('main/groupAdminList') --}}
                    <!-- 代わりにプレースホルダー -->
                    <div id="groupAdmin" class="tab-pane hidden">
                        <div id="groupAdminContent">読み込み中...</div>
                    </div>
                @endif
                
                {{-- @include('main/groupList') --}}
                <!-- 代わりにプレースホルダー -->
                <div id="group" class="tab-pane hidden">
                    <div id="groupContent">読み込み中...</div>
                </div>

                @include('main/userList')

                @if($groupPermissionInfo['useProvisionalFlow'])
                    @include('main/userTempList')
                @endif
                
                @include('main/usageStatus')
            </div>
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

<script>
function reloadWithHash(tabId) {
    // グループタブのフラグをリセット
    if (tabId === 'group') {
        groupLoaded = false;
    }
    if (tabId === 'groupAdmin') {
        groupAdminLoaded = false;
    }
    
    fetch('/set-hash', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ hash: tabId })
    }).then(() => {
        window.location.reload();
    });
}

// グループの読み込み状態を管理する変数
let groupLoaded = false;
let groupAdminLoaded = false;

// ページ読み込み時にhashをチェックして自動読み込み
document.addEventListener("DOMContentLoaded", function() {
    function initializeTabs() {
        document.querySelectorAll('.tab-pane').forEach(function(content) {
            content.classList.add('hidden');
        });
    }
    
    function showTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(function(content) {
            content.classList.add('hidden');
        });
        
        document.querySelectorAll('.nav-link').forEach(function(link) {
            link.classList.remove('bg-green-600');
            link.classList.add('hover:bg-green-600');
        });
        
        const targetContent = document.getElementById(tabId);
        if (targetContent) {
            targetContent.classList.remove('hidden');
        }
        
        const activeTab = document.querySelector(`a[href="#${tabId}"]`);
        if (activeTab) {
            activeTab.classList.add('bg-green-600');
            activeTab.classList.remove('hover:bg-green-600');
        }
    }
    
    // グループコンテンツを読み込む関数
    function loadGroupContent(callback) {
        if (groupLoaded) {
            if (callback) callback();
            return;
        }
        
        fetch('/get-group-content')
            .then(response => response.text())
            .then(html => {
                const container = document.getElementById('groupContent');
                container.innerHTML = html;
                
                // スクリプトを手動で実行
                const scripts = container.getElementsByTagName('script');
                Array.from(scripts).forEach(script => {
                    const newScript = document.createElement('script');
                    if (script.src) {
                        newScript.src = script.src;
                    } else {
                        newScript.textContent = script.textContent;
                    }
                    document.body.appendChild(newScript);
                });
                
                groupLoaded = true;
                if (callback) callback();
            })
            .catch(error => {
                console.error('Error loading group content:', error);
            });
    }
    
    // グループアドミンコンテンツを読み込む関数
    function loadGroupAdminContent(callback) {
        if (groupAdminLoaded) {
            if (callback) callback();
            return;
        }
        
        fetch('/get-group-admin-content')
            .then(response => response.text())
            .then(html => {
                const container = document.getElementById('groupAdminContent');
                container.innerHTML = html;
                
                // スクリプトを手動で実行
                const scripts = container.getElementsByTagName('script');
                Array.from(scripts).forEach(script => {
                    const newScript = document.createElement('script');
                    if (script.src) {
                        newScript.src = script.src;
                    } else {
                        newScript.textContent = script.textContent;
                    }
                    document.body.appendChild(newScript);
                });
                
                groupAdminLoaded = true;
                if (callback) callback();
            })
            .catch(error => {
                console.error('Error loading group admin content:', error);
            });
    }
    
    var hash = @json($hash ?? 'user-internal');
    
    initializeTabs();
    
    // 初期表示時にhashに応じてコンテンツを読み込む
    if (hash === 'group') {
        loadGroupContent(function() {
            showTab(hash);
        });
    } else if (hash === 'groupAdmin') {
        loadGroupAdminContent(function() {
            showTab(hash);
        });
    } else if (hash) {
        showTab(hash);
    }
    
    document.querySelectorAll('.nav-link').forEach(function(tab) {
        tab.addEventListener('click', function(event) {
            event.preventDefault();
            var newHash = this.getAttribute('href').substring(1);
            
            // グループ管理者タブがクリックされた時
            if (newHash === 'groupAdmin') {
                loadGroupAdminContent(function() {
                    showTab(newHash);
                    history.pushState(null, null, '#' + newHash);
                    window.scrollTo(0, 0);
                });
                return;
            }

            // グループタブがクリックされた時
            if (newHash === 'group') {
                loadGroupContent(function() {
                    showTab(newHash);
                    history.pushState(null, null, '#' + newHash);
                    window.scrollTo(0, 0);
                });
                return;
            }
            
            // 通常のタブ切り替え
            showTab(newHash);
            history.pushState(null, null, '#' + newHash);
            window.scrollTo(0, 0);
        });
    });
});
</script>