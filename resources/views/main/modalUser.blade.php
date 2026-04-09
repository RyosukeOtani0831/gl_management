<!-- TailwindCSS モーダル構造 -->
<div class="fixed z-50 inset-0 overflow-y-auto hidden" id="userModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- 背景オーバーレイ -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- モーダルの中身 -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="userModalForm" role="form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="userModalTitle">
                        モーダルタイトル
                    </h3>
                    <div id="userModalBody">
                        <!-- 動的に内容が挿入される -->
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="modalSubmitBtn" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        送信
                    </button>
                    <button type="button" id="closeModalBtn"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        閉じる
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUserModal(type) {
    let modalTitle = "";
    let modalBody = "";
    let formAction = "";
    let submitText = "";
    let formId = "";

    // モーダルの種類に応じて内容を設定
    switch (type) {
        case 'userAdd':
            modalTitle = "ユーザー作成";
            modalBody = `
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">氏名:</label>
                        <input type="text" name="name" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="kana" class="block text-gray-700 text-sm font-bold mb-2">氏名（フリガナ）:</label>
                        <input type="text" name="kana" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                        <input type="email" name="email" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="team" class="block text-gray-700 text-sm font-bold mb-2">Team:</label>
                        <select name="teamId" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @foreach($teamList as $team)
                                <option value="{{$team['id']}}">{{$team['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="accountType" class="block text-gray-700 text-sm font-bold mb-2">アカウント種別:</label>
                        <select name="accountType" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="internal">内部</option>
                            <option value="external">外部</option>
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">利用開始日:</label>
                        <input type="date" name="validFrom"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">利用終了日:</label>
                        <input type="date" name="validTo"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
            `;
            formAction = "{{ action('MainController@createUser') }}";
            submitText = "作成";
            formId = "userAddForm";
            break;

        case 'userCsvAdd':
            modalTitle = "CSVインポート（一括登録）";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">CSVファイルを選択してください</p>
                    <input type="file" name="userCsvFile" required 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>
            `;
            formAction = "{{ action('CsvController@importUserCsv') }}";
            submitText = "インポート";
            formId = "userCsvForm";
            break;

        case 'userCsvEdit':
            modalTitle = "CSVインポート（一括編集）";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">CSVファイルを選択してください</p>
                    <input type="file" name="userCsvEditFile" required 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>
            `;
            formAction = "{{ action('CsvController@importUserEditCsv') }}";
            submitText = "インポート";
            formId = "userCsvEditForm";
            break;

        case 'userCsvDel':
            modalTitle = "CSVインポート（一括削除）";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">CSVファイルを選択してください</p>
                    <input type="file" name="userCsvDelFile" required 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>
            `;
            formAction = "{{ action('CsvController@importUserDelCsv') }}";
            submitText = "インポート";
            formId = "userCsvDelForm";
            break;

        case 'userDel':
            modalTitle = "ユーザー削除";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">選択したユーザーを削除しますか？</p>
                    <div id="delUserModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@deleteUser') }}";
            submitText = "削除";
            formId = "userDelForm";
            break;

        case 'userEdit':
            modalTitle = "ユーザ編集";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">変更を確定しますか？</p>
                    <div id="editUserModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@editUser') }}";
            submitText = "確定";
            formId = "userEditForm";
            break;
        
        case 'userEditCancel':
            modalTitle = "編集キャンセル";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">編集を破棄しますか？</p>
                    <div id="returnMainModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@drawMain') }}";
            submitText = "破棄";
            formId = "userEditCancelForm";
            break;
        
        default:
            modalTitle = "不明なモーダル";
            modalBody = "<p class='text-gray-700'>無効なリクエストです。</p>";
            formAction = "#";
            submitText = "送信";
            formId = "defaultForm";
            break;
    }

    // モーダルのタイトルと内容を更新
    document.getElementById('userModalTitle').innerText = modalTitle;
    document.getElementById('userModalBody').innerHTML = modalBody;
    // userAddの場合、現在のタブに応じてaccountTypeの初期値をセット
    if (type === 'userAdd') {
        const accountTypeSelect = document.querySelector('#userModalBody select[name="accountType"]');
        if (accountTypeSelect) {
            const isExternal = !document.getElementById('user-external').classList.contains('hidden');
            accountTypeSelect.value = isExternal ? 'external' : 'internal';
        }
    }
    // 現在のタブ情報をhiddenフィールドで送信
    const existingHashInput = document.getElementById('currentHashInput');
    if (existingHashInput) existingHashInput.remove();
    const hashInput = document.createElement('input');
    hashInput.type = 'hidden';
    hashInput.id = 'currentHashInput';
    hashInput.name = 'current_hash';
    const isExternal = !document.getElementById('user-external').classList.contains('hidden');
    hashInput.value = isExternal ? 'user-external' : 'user-internal';
    const modalForm = document.getElementById('userModalForm');
    if (modalForm) modalForm.appendChild(hashInput);
    document.getElementById('modalSubmitBtn').innerText = submitText;

    // 送信ボタンのスタイルを動的に変更
    const submitBtn = document.getElementById('modalSubmitBtn');
    if (type === 'userDel' || type === 'userEditCancel') {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm";
    } else {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm";
    }

    // フォームのアクションとIDを設定
    var form = document.getElementById('userModalForm');
    if (form) {
        form.action = formAction;
        form.id = formId;
    }

    // モーダルを表示
    const userModal = document.getElementById('userModal');
    if (userModal) {
        userModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // 特定のモーダルタイプに応じた追加処理
    switch (type) {
        case 'userEdit':
            editUserList();
            break;
        case 'userDel':
            delUserList();
            break;
    }
}

// モーダルが閉じられた際にフォームをリセット
const closeModalBtn = document.getElementById('closeModalBtn');
if (closeModalBtn) {
    closeModalBtn.addEventListener('click', function() {
        const userModal = document.getElementById('userModal');
        const form = document.getElementById('userModalForm');
        
        // モーダルを非表示にする
        if (userModal) {
            userModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // フォームのリセット
        if (form) {
            form.reset();
        }
    });
}

// 背景クリックでモーダルを閉じる
document.addEventListener('click', function(event) {
    const userModal = document.getElementById('userModal');
    if (event.target === userModal) {
        userModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
});

function editUserList(e){
    var checkList = document.getElementsByClassName('delUserTarget');
    var userId = "";
    var usersInfo = [];

    // チームIDリスト取得
    for (let i = 0; i < checkList.length; i += 1) {
        if(checkList[i].checked) {
            
            userId = checkList[i].value;

            var userInfo = new Object();
            userInfo.id = userId;
            userInfo.displayName = document.getElementsByName("userName" + userId)[0].value;
            userInfo.kana = document.getElementsByName("userKana" + userId)[0].value;
            userInfo.emailAddress = document.getElementsByName("userEmail" + userId)[0].value;

            const targetSelect = document.getElementsByName("userTeamId" + userId)[0];        
            userInfo.teamId = targetSelect.options[targetSelect.selectedIndex].value;

            userInfo.validFrom = document.getElementsByName("userValidFrom" + userId)[0].value;
            userInfo.validTo = document.getElementsByName("userValidTo" + userId)[0].value;
            userInfo.description = document.getElementsByName("userDescription" + userId)[0].value;
            userInfo.authDescription = document.getElementsByName("userAuthDescription" + userId)[0].value;
            const accountTypeEl = document.getElementsByName("userAccountType" + userId)[0];
            userInfo.accountType = accountTypeEl ? accountTypeEl.value : "internal";
            
            usersInfo.push(userInfo);
        }
    }
    var data = JSON.stringify(usersInfo);

    let body = document.getElementById('editUserModalBody');
    body.insertAdjacentHTML('afterend', '<input type="hidden" name="usersInfo" value=\'' + data + '\'/>');
}

function delUserList(e) {
    var checkList = document.getElementsByClassName('delUserTarget');
    let body = document.getElementById('delUserModalBody');
    var userIds = [];

    // チェックされたユーザーIDを収集
    for (let i = 0; i < checkList.length; i++) {
        if (checkList[i].checked === true) {
            userIds.push(checkList[i].value);
        }
    }

    // ユーザーIDが存在する場合のみ hidden input を追加
    if (userIds.length > 0) {
        body.insertAdjacentHTML(
            'afterend',
            '<input type="hidden" name="userIds" value="' + userIds.join(',') + '" />'
        );
    }
}
</script>