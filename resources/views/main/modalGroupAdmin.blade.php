<!-- TailwindCSS グループ管理者モーダル -->
<div class="fixed z-50 inset-0 overflow-y-auto hidden" id="groupAdminModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- 背景オーバーレイ -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- モーダルの中身 -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="groupAdminModalForm" role="form" method="post">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="groupAdminModalTitle">
                        ケース管理者操作
                    </h3>
                    <div id="groupAdminModalBody">
                        <!-- 動的に内容が挿入される -->
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="groupAdminModalSubmitBtn" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        実行
                    </button>
                    <button type="button" id="groupAdminCloseModalBtn"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        閉じる
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openGroupAdminModal(type) {
    let modalTitle = "";
    let modalBody = "";
    let formAction = "";
    let submitText = "";

    switch (type) {
        case 'groupAdminDel':
            modalTitle = "ケース削除";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">選択したケースを削除しますか？</p>
                    <div id="delGroupAdminModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@deleteGroupAdmin') }}";
            submitText = "削除";
            break;

        case 'groupAdminEdit':
            modalTitle = "ケース（管理者）編集";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">変更を確定しますか？</p>
                    <div id="editGroupAdminModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@editGroupAdmin') }}";
            submitText = "確定";
            break;
        
        case 'groupAdminEditCancel':
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
            modalTitle = "ケース管理者操作";
            modalBody = "<p class='text-gray-700'>無効なリクエストです。</p>";
            formAction = "#";
            submitText = "実行";
            break;
    }

    // モーダルのタイトルと内容を更新
    document.getElementById('groupAdminModalTitle').innerText = modalTitle;
    document.getElementById('groupAdminModalBody').innerHTML = modalBody;
    document.getElementById('groupAdminModalSubmitBtn').innerText = submitText;

    // 送信ボタンのスタイルを動的に変更
    const submitBtn = document.getElementById('groupAdminModalSubmitBtn');
    if (type === 'groupAdminDel' || type === 'groupAdminEditCancel') {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm";
    } else {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm";
    }

    // フォームのアクションを設定
    var form = document.getElementById('groupAdminModalForm');
    if (form) {
        form.action = formAction;
    }

    // モーダルを表示
    const groupAdminModal = document.getElementById('groupAdminModal');
    if (groupAdminModal) {
        groupAdminModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // 特定のモーダルタイプに応じた追加処理
    switch (type) {
        case 'groupAdminEdit':
            editGroupAdminList();
            break;
        case 'groupAdminDel':
            delGroupAdminList();
            break;
    }
}

// モーダルを閉じる
function closeGroupAdminModal() {
    const groupAdminModal = document.getElementById('groupAdminModal');
    const form = document.getElementById('groupAdminModalForm');

    if (groupAdminModal) {
        groupAdminModal.classList.add('hidden');
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
        const closeBtn = document.getElementById('groupAdminCloseModalBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeGroupAdminModal);
        }

        // 背景クリックでモーダルを閉じる
        const groupAdminModal = document.getElementById('groupAdminModal');
        if (groupAdminModal) {
            groupAdminModal.addEventListener('click', function(event) {
                if (event.target === groupAdminModal) {
                    closeGroupAdminModal();
                }
            });
        }
    }, 100);
})();

// ESCキーでモーダルを閉じる（グローバルなので一度だけ登録）
if (!window.groupAdminEscListenerAdded) {
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const groupAdminModal = document.getElementById('groupAdminModal');
            if (groupAdminModal && !groupAdminModal.classList.contains('hidden')) {
                closeGroupAdminModal();
            }
        }
    });
    window.groupAdminEscListenerAdded = true;
}

// 削除関数（元の仕様に基づく）
function delGroupAdminList() {
    var checkList = document.getElementsByClassName('delGroupAdminTarget');
    let body = document.getElementById('delGroupAdminModalBody');
    var groupAdminIds = "";
    var groupAdminId = "";

    for (let i = 0; i < checkList.length; i++) {
        if (checkList[i].checked === true) {
            groupAdminId = "";

            if (groupAdminIds.length > 0) {
                groupAdminId += ",";
            }
            groupAdminId += checkList[i].value; // value を使用
            groupAdminIds += groupAdminId;
        }
    }
    
    if (body) {
        body.insertAdjacentHTML('afterend', '<input type="hidden" name="groupAdminIds" value="' + groupAdminIds + '" />');
    }
}

// 編集関数（元の仕様に基づく）
function editGroupAdminList() {
    var checkList = document.getElementsByClassName('delGroupAdminTarget');
    var groupAdminsInfo = [];

    for (let i = 0; i < checkList.length; i++) {
        if (checkList[i].checked) {
            var groupAdminId = checkList[i].value; // value を使用
            
            var groupAdminInfo = {};
            groupAdminInfo.id = groupAdminId;
            
            var indexGroupAdminPublicSelect = document.getElementById("groupAdminPublicSelect" + groupAdminId).selectedIndex;
            groupAdminInfo.public = document.getElementsByName("groupAdminPublic" + groupAdminId)[indexGroupAdminPublicSelect].value;
            groupAdminInfo.name = document.getElementsByName("groupAdminName" + groupAdminId)[0].value;
            groupAdminInfo.avatarFileId = document.getElementsByName("groupAdminAvatarFileId" + groupAdminId)[0].value;
            
            var checks = document.getElementsByName('groupAdminMember' + groupAdminId);
            groupAdminInfo.usersIds = [];

            for (let j = 0; j < checks.length; j++) {
                if (checks[j].checked === true) {
                    groupAdminInfo.usersIds.push(parseInt(checks[j].value));
                }
            }

            groupAdminsInfo.push(groupAdminInfo);
        }
    }
    
    var data = JSON.stringify(groupAdminsInfo);
    let body = document.getElementById('editGroupAdminModalBody');
    if (body) {
        body.insertAdjacentHTML('afterend', '<input type="hidden" name="groupAdminsInfo" value=\'' + data + '\'/>');
    }
}
</script>