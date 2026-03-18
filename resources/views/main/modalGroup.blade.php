<!-- TailwindCSS グループモーダル -->
<div class="fixed z-50 inset-0 overflow-y-auto hidden" id="groupModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- 背景オーバーレイ -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- モーダルの中身 -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="groupModalForm" role="form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="groupModalTitle">
                        ケース操作
                    </h3>
                    <div id="groupModalBody">
                        <!-- 動的に内容が挿入される -->
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="groupModalSubmitBtn" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        実行
                    </button>
                    <button type="button" id="groupCloseModalBtn"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        閉じる
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openGroupModal(type) {
    let modalTitle = "";
    let modalBody = "";
    let formAction = "";
    let submitText = "";

    switch (type) {
        case 'groupAdd':
            modalTitle = "ケース追加";
            modalBody = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">ケース名:</label>
                        <input type="text" name="name" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">チーム:</label>
                        <select name="teamId" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value=""></option>
                            @foreach($teamList as $team)
                                <option value="{{$team['id']}}">{{$team['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            `;
            formAction = "{{ action('MainController@createGroup') }}";
            submitText = "追加";
            break;

        case 'groupCsvAdd':
            modalTitle = "CSVインポート（ケース一括登録）";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">CSVファイルを選択してください</p>
                    <input type="file" name="groupCsvFile" required 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>
            `;
            formAction = "{{ action('CsvController@importGroupCsv') }}";
            submitText = "インポート";
            break;

        case 'groupDel':
            modalTitle = "ケース削除";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">選択したケースを削除しますか？</p>
                    <div id="delGroupModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@deleteGroup') }}";
            submitText = "削除";
            break;

        case 'groupEdit':
            modalTitle = "ケース編集";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">変更を確定しますか？</p>
                    <div id="editGroupModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@editGroup') }}";
            submitText = "確定";
            break;
        
        case 'groupEditCancel':
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
            modalTitle = "ケース操作";
            modalBody = "<p class='text-gray-700'>無効なリクエストです。</p>";
            formAction = "#";
            submitText = "実行";
            break;
    }

    // モーダルのタイトルと内容を更新
    document.getElementById('groupModalTitle').innerText = modalTitle;
    document.getElementById('groupModalBody').innerHTML = modalBody;
    document.getElementById('groupModalSubmitBtn').innerText = submitText;

    // 送信ボタンのスタイルを動的に変更
    const submitBtn = document.getElementById('groupModalSubmitBtn');
    if (type === 'groupDel' || type === 'groupEditCancel') {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm";
    } else {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm";
    }

    // フォームのアクションを設定
    var form = document.getElementById('groupModalForm');
    if (form) {
        form.action = formAction;
    }

    // モーダルを表示
    const groupModal = document.getElementById('groupModal');
    if (groupModal) {
        groupModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // 特定のモーダルタイプに応じた追加処理
    switch (type) {
        case 'groupEdit':
            editGroupList();
            break;
        case 'groupDel':
            delGroupList();
            break;
    }
}

// モーダルを閉じる
function closeGroupModal() {
    const groupModal = document.getElementById('groupModal');
    const form = document.getElementById('groupModalForm');
    
    if (groupModal) {
        groupModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if (form) {
        form.reset();
    }
}

// 閉じるボタンのイベントリスナー（即座に実行）
(function() {
    // ちょっと待ってからイベントリスナーを登録（DOM構築を待つ）
    setTimeout(function() {
        const closeBtn = document.getElementById('groupCloseModalBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeGroupModal);
        }

        // 背景クリックでモーダルを閉じる
        const groupModal = document.getElementById('groupModal');
        if (groupModal) {
            groupModal.addEventListener('click', function(event) {
                if (event.target === groupModal) {
                    closeGroupModal();
                }
            });
        }
    }, 100);
})();

// ESCキーでモーダルを閉じる（グローバルなので一度だけ登録）
if (!window.groupEscListenerAdded) {
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const groupModal = document.getElementById('groupModal');
            if (groupModal && !groupModal.classList.contains('hidden')) {
                closeGroupModal();
            }
        }
    });
    window.groupEscListenerAdded = true;
}

// 既存の関数との互換性保持
function editGroupList() {
    var checkList = document.getElementsByClassName('delGroupTarget');
    var groupsInfo = [];

    for (let i = 0; i < checkList.length; i++) {
        if(checkList[i].checked) {
            var groupId = checkList[i].value; // 元は checkList[i].name だったが value の方が一般的
            
            var groupInfo = {
                id: groupId,
                name: document.getElementsByName("groupName" + groupId)[0].value,
                teamId: document.getElementsByName("groupTeamId" + groupId)[0].value,
                public: document.getElementsByName("groupPublic" + groupId)[0].value
            };
            
            // avatarFileId フィールドが存在する場合のみ追加
            const avatarField = document.getElementsByName("groupAvatarFileId" + groupId)[0];
            if (avatarField) {
                groupInfo.avatarFileId = avatarField.value;
            }
            
            // グループメンバー選択がある場合（現在のgroupList.blade.phpにはないが互換性のため）
            var memberChecks = document.getElementsByName('groupMember' + groupId);
            if (memberChecks && memberChecks.length > 0) {
                groupInfo.usersIds = [];
                for (let j = 0; j < memberChecks.length; j++) {
                    if (memberChecks[j].checked === true) {
                        groupInfo.usersIds.push(parseInt(memberChecks[j].value));
                    }
                }
            }
            
            groupsInfo.push(groupInfo);
        }
    }
    
    var data = JSON.stringify(groupsInfo);
    let body = document.getElementById('editGroupModalBody');
    if (body) {
        body.insertAdjacentHTML('afterend', '<input type="hidden" name="groupsInfo" value=\'' + data + '\'/>');
    }
}

function delGroupList() {
    var checkList = document.getElementsByClassName('delGroupTarget');
    let body = document.getElementById('delGroupModalBody');
    var groupIds = [];

    for (let i = 0; i < checkList.length; i++) {
        if (checkList[i].checked === true) {
            groupIds.push(checkList[i].value);
        }
    }

    if (groupIds.length > 0 && body) {
        body.insertAdjacentHTML(
            'afterend',
            '<input type="hidden" name="groupIds" value="' + groupIds.join(',') + '" />'
        );
    }
}
</script>