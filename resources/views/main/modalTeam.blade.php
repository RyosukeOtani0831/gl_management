<!-- TailwindCSS チームモーダル -->
<div class="fixed z-50 inset-0 overflow-y-auto hidden" id="teamModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- 背景オーバーレイ -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- モーダルの中身 -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="teamModalForm" role="form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="teamModalTitle">
                        チーム操作
                    </h3>
                    <div id="teamModalBody">
                        <!-- 動的に内容が挿入される -->
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" id="teamModalSubmitBtn" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        実行
                    </button>
                    <button type="button" id="teamCloseModalBtn"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        閉じる
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openTeamModal(type) {
    let modalTitle = "";
    let modalBody = "";
    let formAction = "";
    let submitText = "";

    switch (type) {
        case 'teamAdd':
            modalTitle = "チーム作成";
            modalBody = `
                <div class="space-y-4">
                    <div>
                        <label for="teamName" class="block text-gray-700 text-sm font-bold mb-2">チーム名:</label>
                        <input type="text" name="name" required
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
            `;
            formAction = "{{ action('MainController@createTeam') }}";
            submitText = "作成";
            break;

        case 'teamCsvAdd':
            modalTitle = "CSVインポート（チーム一括登録）";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">CSVファイルを選択してください</p>
                    <input type="file" name="teamCsvFile" required 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                </div>
            `;
            formAction = "{{ action('CsvController@importTeamCsv') }}";
            submitText = "インポート";
            break;

        case 'teamDel':
            modalTitle = "チーム削除";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">選択したチームを削除しますか？</p>
                    <div id="delTeamModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@deleteTeam') }}";
            submitText = "削除";
            break;

        case 'teamEdit':
            modalTitle = "チーム編集";
            modalBody = `
                <div class="space-y-4">
                    <p class="text-gray-700">変更を確定しますか？</p>
                    <div id="editTeamModalBody" class="text-sm text-gray-600"></div>
                </div>
            `;
            formAction = "{{ action('MainController@editTeam') }}";
            submitText = "確定";
            break;
        
        case 'teamEditCancel':
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
            modalTitle = "チーム操作";
            modalBody = "<p class='text-gray-700'>無効なリクエストです。</p>";
            formAction = "#";
            submitText = "実行";
            break;
    }

    // モーダルのタイトルと内容を更新
    document.getElementById('teamModalTitle').innerText = modalTitle;
    document.getElementById('teamModalBody').innerHTML = modalBody;
    document.getElementById('teamModalSubmitBtn').innerText = submitText;

    // 送信ボタンのスタイルを動的に変更
    const submitBtn = document.getElementById('teamModalSubmitBtn');
    if (type === 'teamDel' || type === 'teamEditCancel') {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm";
    } else {
        submitBtn.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm";
    }

    // フォームのアクションとエンコードタイプを設定
    var form = document.getElementById('teamModalForm');
    if (form) {
        form.action = formAction;
        
        // CSVアップロードの場合はenctypeを設定
        if (type === 'teamCsvAdd') {
            form.enctype = 'multipart/form-data';
        } else {
            form.removeAttribute('enctype');
        }
    }

    // モーダルを表示
    const teamModal = document.getElementById('teamModal');
    if (teamModal) {
        teamModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    // 特定のモーダルタイプに応じた追加処理
    switch (type) {
        case 'teamEdit':
            editTeamList();
            break;
        case 'teamDel':
            delTeamList();
            break;
    }
}

// モーダルを閉じる
function closeTeamModal() {
    const teamModal = document.getElementById('teamModal');
    const form = document.getElementById('teamModalForm');
    
    if (teamModal) {
        teamModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    if (form) {
        form.reset();
    }
}

// 閉じるボタンのイベントリスナー
document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.getElementById('teamCloseModalBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeTeamModal);
    }

    // 背景クリックでモーダルを閉じる
    document.addEventListener('click', function(event) {
        const teamModal = document.getElementById('teamModal');
        if (event.target === teamModal) {
            closeTeamModal();
        }
    });

    // ESCキーでモーダルを閉じる
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const teamModal = document.getElementById('teamModal');
            if (teamModal && !teamModal.classList.contains('hidden')) {
                closeTeamModal();
            }
        }
    });
});

// 既存の関数との互換性保持
function editTeamList() {
    var checkList = document.getElementsByClassName('delTeamTarget');
    var teamsInfo = [];

    for (let i = 0; i < checkList.length; i++) {
        if(checkList[i].checked) {
            var teamId = checkList[i].value;
            var teamInfo = {
                id: teamId,
                name: document.getElementsByName("teamName" + teamId)[0].value
            };
            teamsInfo.push(teamInfo);
        }
    }
    
    var data = JSON.stringify(teamsInfo);
    let body = document.getElementById('editTeamModalBody');
    if (body) {
        body.insertAdjacentHTML('afterend', '<input type="hidden" name="teamsInfo" value=\'' + data + '\'/>');
    }
}

function delTeamList() {
    var checkList = document.getElementsByClassName('delTeamTarget');
    let body = document.getElementById('delTeamModalBody');
    var teamIds = [];

    for (let i = 0; i < checkList.length; i++) {
        if (checkList[i].checked === true) {
            teamIds.push(checkList[i].value);
        }
    }

    if (teamIds.length > 0 && body) {
        body.insertAdjacentHTML(
            'afterend',
            '<input type="hidden" name="teamIds" value="' + teamIds.join(',') + '" />'
        );
    }
}
</script>