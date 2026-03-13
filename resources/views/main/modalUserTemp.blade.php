<!-- TailwindCSS ユーザー一時モーダル -->
<div class="fixed z-50 inset-0 overflow-y-auto hidden" id="userTempModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- 背景オーバーレイ -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- モーダルの中身 -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="userTempModalForm" role="form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="userTempModalTitle">
                        ユーザー一時操作
                    </h3>
                    <div id="userTempModalBody">
                        <!-- 動的に内容が挿入される -->
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="userTempModalSubmitBtn" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        実行
                    </button>
                    <button type="button" id="userTempCloseModalBtn"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        閉じる
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUserTempModal(type) {
    let modalTitle = "";
    let modalBody = "";
    let formAction = "";
    let submitText = "";

    switch (type) {
        case 'userTempAdd':
            modalTitle = "ユーザー作成";
            modalBody = `
                <div class="space-y-4">
                    <div>
                        <label for="userTempName" class="block text-gray-700 text-sm font-bold mb-2">氏名:</label>
                        <input type="text" name="name" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="userTempKana" class="block text-gray-700 text-sm font-bold mb-2">氏名（フリガナ）:</label>
                        <input type="text" name="kana" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="userTempPassword" class="block text-gray-700 text-sm font-bold mb-2">パスワード:</label>
                        <input type="text" name="password" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="userTempTeam" class="block text-gray-700 text-sm font-bold mb-2">Team:</label>
                        <select name="team" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @foreach($teamList as $team)
                                <option value="{{is_object($team) ? $team->id : $team['id']}}">{{is_object($team) ? $team->name : $team['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            `;
            formAction = "{{ action('MainController@createUserTemp') }}";
            submitText = "作成";
            break;

        case 'userTempCsvAdd':
            modalTitle = "CSVインポート（仮ユーザー一括登録）";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">CSVファイルを選択してください</p>
                    <input type="file" name="userTempCsvFile" accept=".csv" required 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>
            `;
            formAction = "{{ action('CsvController@importUserTempCsv') }}";
            submitText = "インポート";
            break;

        case 'userTempDel':
            modalTitle = "ユーザー削除";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">選択したユーザーを削除しますか？</p>
                    <div id="delUserTempModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@deleteUserTemp') }}";
            submitText = "削除";
            break;

        case 'userTempEdit':
            modalTitle = "仮ユーザ編集";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">変更を確定しますか？</p>
                    <div id="editUserTempModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@editUserTemp') }}";
            submitText = "確定";
            break;
        
        case 'userTempEditCancel':
            modalTitle = "編集キャンセル";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">編集を破棄しますか？</p>
                </div>
            `;
            formAction = "{{ action('MainController@drawMain') }}";
            submitText = "破棄";
            break;
        
        default:
            modalTitle = "ユーザー一時操作";
            modalBody = "<p class='text-gray-700'>無効なリクエストです。</p>";
            formAction = "#";
            submitText = "実行";
            break;
    }

    // モーダルのタイトルと内容を更新
    document.getElementById('userTempModalTitle').innerText = modalTitle;
    document.getElementById('userTempModalBody').innerHTML = modalBody;
    document.getElementById('userTempModalSubmitBtn').innerText = submitText;

    // 送信ボタンのスタイルを動的に変更
    const submitBtn = document.getElementById('userTempModalSubmitBtn');
    if (type === 'userTempDel' || type === 'userTempEditCancel') {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm";
    } else {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm";
    }

    // フォームのアクションとエンコードタイプを設定
    var form = document.getElementById('userTempModalForm');
    if (form) {
        form.action = formAction;
        
        // CSVアップロードの場合はenctypeを設定
        if (type.includes('Csv')) {
            form.enctype = 'multipart/form-data';
        } else {
            form.removeAttribute('enctype');
        }
    }

    // モーダルを表示
    const userTempModal = document.getElementById('userTempModal');
    if (userTempModal) {
        userTempModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // 特定のモーダルタイプに応じた追加処理
    switch (type) {
        case 'userTempEdit':
            editUserTempList();
            break;
        case 'userTempDel':
            delUserTempList();
            break;
    }
}

// モーダルを閉じる
function closeUserTempModal() {
    const userTempModal = document.getElementById('userTempModal');
    const form = document.getElementById('userTempModalForm');
    
    if (userTempModal) {
        userTempModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if (form) {
        form.reset();
    }
}

// 閉じるボタンのイベントリスナー
document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.getElementById('userTempCloseModalBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeUserTempModal);
    }

    // 背景クリックでモーダルを閉じる
    document.addEventListener('click', function(event) {
        const userTempModal = document.getElementById('userTempModal');
        if (event.target === userTempModal) {
            closeUserTempModal();
        }
    });

    // ESCキーでモーダルを閉じる
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const userTempModal = document.getElementById('userTempModal');
            if (userTempModal && !userTempModal.classList.contains('hidden')) {
                closeUserTempModal();
            }
        }
    });
});

// 既存の関数との互換性保持（バグ修正版）
function editUserTempList() {
    var checkList = document.getElementsByClassName('delUserTempTarget');
    var userTempsInfo = [];

    for (let i = 0; i < checkList.length; i++) {
        if(checkList[i].checked) {
            var userTempId = checkList[i].value; // name → value に修正
            
            var userTempInfo = {
                id: userTempId,
                name: document.getElementsByName("userTempName" + userTempId)[0].value,
                kana: document.getElementsByName("userTempKana" + userTempId)[0].value
            };
            
            // passwordフィールドが存在する場合のみ追加
            const passwordField = document.getElementsByName("userTempPassword" + userTempId)[0];
            if (passwordField) {
                userTempInfo.password = passwordField.value;
            }
            
            // team選択の処理
            const select = document.getElementsByName("userTempTeamId" + userTempId)[0];
            if (select && select.selectedIndex !== undefined) {
                userTempInfo.teamId = select.options[select.selectedIndex].value;
            }
            
            userTempsInfo.push(userTempInfo);
        }
    }
    
    var data = JSON.stringify(userTempsInfo);
    let body = document.getElementById('editUserTempModalBody');
    if (body) {
        body.insertAdjacentHTML('afterend', '<input type="hidden" name="userTempsInfo" value=\'' + data + '\'/>');
    }
}

function delUserTempList() {
    var checkList = document.getElementsByClassName('delUserTempTarget');
    let body = document.getElementById('delUserTempModalBody');
    var userTempIds = [];

    for (let i = 0; i < checkList.length; i++) {
        if (checkList[i].checked === true) {
            userTempIds.push(checkList[i].value); // name → value に修正
        }
    }

    if (userTempIds.length > 0 && body) {
        body.insertAdjacentHTML(
            'afterend',
            '<input type="hidden" name="userTempIds" value="' + userTempIds.join(',') + '" />'
        );
    }
}
</script>