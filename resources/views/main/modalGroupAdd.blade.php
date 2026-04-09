<!-- ケース作成モーダル -->
<div class="fixed z-50 inset-0 overflow-y-auto hidden" id="groupAddModal">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form id="groupAddForm" role="form" method="post" action="{{ action('MainController@createGroup') }}">
                @csrf
                <div class="bg-white px-6 pt-5 pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">ケース作成</h3>

                    <!-- ケース名 -->
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">ケース名 <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required
                               class="w-full border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <!-- チーム -->
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-1">チーム <span class="text-red-500">*</span></label>
                        <select name="teamId" required
                                class="w-full border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            @foreach($teamList as $team)
                                <option value="{{ $team['id'] }}">{{ $team['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <hr class="my-4">

                    <!-- 既存ユーザー追加セクション -->
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">既存ユーザーを追加</label>
                        <div class="flex gap-2 mb-2">
                            <input type="text" id="existingUserSearch" placeholder="名前またはメールで検索..."
                                   oninput="filterExistingUsers(this.value)"
                                   class="flex-1 border rounded py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div id="existingUserList" class="border rounded max-h-40 overflow-y-auto bg-white">
                            @foreach($userListAll as $user)
                                <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer existing-user-row"
                                       data-name="{{ $user['displayName'] }}"
                                       data-email="{{ $user['emailAddress'] ?? '' }}">
                                    <input type="checkbox" name="existing_user_ids[]" value="{{ $user['id'] }}"
                                           class="existing-user-checkbox">
                                    <span class="text-sm text-gray-700">{{ $user['displayName'] }}</span>
                                    <span class="text-xs text-gray-400">{{ $user['emailAddress'] ?? '' }}</span>
                                    <span class="ml-auto text-xs px-1.5 py-0.5 rounded
                                        {{ ($user['accountType'] ?? 'internal') === 'external'
                                            ? 'bg-orange-100 text-orange-700'
                                            : 'bg-blue-100 text-blue-700' }}">
                                        {{ ($user['accountType'] ?? 'internal') === 'external' ? '外部' : '内部' }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <div id="selectedExistingUsers" class="mt-2 flex flex-wrap gap-1"></div>
                    </div>

                    <hr class="my-4">

                    <!-- 新規外部ユーザー作成セクション -->
                    <div class="mb-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-bold text-gray-700">新規外部ユーザーを作成して追加</label>
                            <button type="button" onclick="addNewUserRow()"
                                    class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                ＋ ユーザーを追加
                            </button>
                        </div>
                        <div id="newUserRows" class="space-y-3"></div>

                        <!-- メール送信チェック（新規ユーザーがいる時だけ表示） -->
                        <div id="sendMailArea" class="hidden mt-3 flex items-center gap-2">
                            <input type="checkbox" name="send_welcome_mail" id="sendWelcomeMail" value="1" checked
                                   class="rounded border-gray-300 text-blue-600">
                            <label for="sendWelcomeMail" class="text-sm text-gray-700">作成した外部ユーザーに登録案内メールを送信する</label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">※ メールアドレスが重複している場合はそのユーザーはスキップされます</p>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex flex-row-reverse gap-2">
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none">
                        作成
                    </button>
                    <button type="button" onclick="closeGroupAddModal()"
                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                        閉じる
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let newUserRowIndex = 0;
const existingEmails = @json(array_column($userListAll, 'emailAddress'));

function openGroupAddModal() {
    document.getElementById('groupAddModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeGroupAddModal() {
    document.getElementById('groupAddModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('groupAddForm').reset();
    document.getElementById('newUserRows').innerHTML = '';
    document.getElementById('selectedExistingUsers').innerHTML = '';
    document.getElementById('existingUserList').classList.add('hidden');
    document.getElementById('existingUserSearch').value = '';
    document.getElementById('sendMailArea').classList.add('hidden');
    newUserRowIndex = 0;
}

// 既存ユーザー検索
function filterExistingUsers(query) {
    const list = document.getElementById('existingUserList');
    const rows = list.querySelectorAll('.existing-user-row');
    const q = query.trim().toLowerCase();

    if (q === '') {
        
        return;
    }
    list.classList.remove('hidden');

    rows.forEach(row => {
        const name = row.dataset.name.toLowerCase();
        const email = row.dataset.email.toLowerCase();
        row.style.display = (name.includes(q) || email.includes(q)) ? '' : 'none';
    });
}

// 既存ユーザーのチェック変更時にタグ表示を更新
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('existing-user-checkbox')) {
        updateSelectedTags();
    }
});

function updateSelectedTags() {
    const container = document.getElementById('selectedExistingUsers');
    const checked = document.querySelectorAll('.existing-user-checkbox:checked');
    container.innerHTML = '';
    checked.forEach(cb => {
        const row = cb.closest('.existing-user-row');
        const name = row.dataset.name;
        const tag = document.createElement('span');
        tag.className = 'inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded';
        tag.innerHTML = `${name} <button type="button" onclick="uncheckUser('${cb.value}')" class="hover:text-red-500">&times;</button>`;
        container.appendChild(tag);
    });
}

function uncheckUser(userId) {
    const cb = document.querySelector(`.existing-user-checkbox[value="${userId}"]`);
    if (cb) {
        cb.checked = false;
        updateSelectedTags();
    }
}

// 新規ユーザー行追加
function addNewUserRow() {
    const idx = newUserRowIndex++;
    const row = document.createElement('div');
    row.id = `newUserRow_${idx}`;
    row.className = 'border rounded p-3 bg-gray-50 relative';
    row.innerHTML = `
        <button type="button" onclick="removeNewUserRow(${idx})"
                class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-lg leading-none">&times;</button>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">氏名 <span class="text-red-500">*</span></label>
                <input type="text" name="new_users[${idx}][name]"
                       class="w-full border rounded py-1.5 px-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400"
                       placeholder="例：山田 太郎" required>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">ふりがな</label>
                <input type="text" name="new_users[${idx}][kana]"
                       class="w-full border rounded py-1.5 px-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400"
                       placeholder="例：やまだ たろう">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-bold text-gray-600 mb-1">メールアドレス <span class="text-red-500">*</span></label>
                <input type="email" name="new_users[${idx}][email]"
                       class="w-full border rounded py-1.5 px-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400"
                       placeholder="例：yamada@example.com" required
>
            </div>
        </div>
    `;
    document.getElementById('newUserRows').appendChild(row);
    document.getElementById('sendMailArea').classList.remove('hidden');
}

function removeNewUserRow(idx) {
    const row = document.getElementById(`newUserRow_${idx}`);
    if (row) row.remove();
    // 行がなくなったらメールチェックも隠す
    if (document.getElementById('newUserRows').children.length === 0) {
        document.getElementById('sendMailArea').classList.add('hidden');
    }
}

// 全角→半角変換 + 先頭ピリオドチェック
function normalizeEmail(input) {
    // 全角英数字・記号を半角に変換
    let val = input.value.replace(/[Ａ-Ｚａ-ｚ０-９！-～]/g, function(s) {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
    });
    // 全角＠を半角@に
    val = val.replace(/＠/g, '@');
    input.value = val;
}

// 既存ユーザーとの重複チェック
function checkDuplicateEmail(input) {
    const val = input.value.trim().toLowerCase();
    const row = input.closest('div[id^="newUserRow_"]');
    let warning = row.querySelector('.email-warning');

    // 先頭ピリオドチェック
    const localPart = val.split('@')[0] ?? '';
    if (localPart.startsWith('.')) {
        if (!warning) {
            warning = document.createElement('p');
            warning.className = 'email-warning text-xs text-red-500 mt-1';
            input.parentNode.appendChild(warning);
        }
        warning.textContent = 'メールアドレスの先頭にピリオドは使用できません';
        return;
    }

    // 既存ユーザーとの重複チェック
    const isDuplicate = val && existingEmails.some(e => e && e.toLowerCase() === val);
    if (isDuplicate) {
        if (!warning) {
            warning = document.createElement('p');
            warning.className = 'email-warning text-xs text-red-500 mt-1';
            input.parentNode.appendChild(warning);
        }
        warning.textContent = 'このメールアドレスは既に登録されています';
    } else {
        if (warning) warning.remove();
    }
}

// submit時に先頭ピリオドの最終チェック
document.addEventListener('submit', function(e) {
    if (e.target.id !== 'groupAddForm') return;
    const emailInputs = document.querySelectorAll('#newUserRows input[type="email"]');
    for (const input of emailInputs) {
        const val = input.value.trim();
        if (!val) continue;
        const localPart = val.split('@')[0] ?? '';
        if (localPart.startsWith('.')) {
            e.preventDefault();
            alert('メールアドレスの先頭にピリオドが含まれている行があります');
            input.focus();
            return;
        }
    }
});

// submit時メールバリデーション
document.addEventListener('submit', function(e) {
    if (e.target.id !== 'groupAddForm') return;
    const emailInputs = document.querySelectorAll('#newUserRows input[name$="[email]"]');
    for (const input of emailInputs) {
        const val = input.value.trim();
        if (!val) continue;
        const localPart = val.split('@')[0] || '';
        if (!val.includes('@')) {
            e.preventDefault();
            alert('メールアドレスに「@」が含まれていない行があります');
            input.focus();
            return;
        }
        if (localPart.startsWith('.')) {
            e.preventDefault();
            alert('メールアドレスの先頭にピリオドが含まれている行があります');
            input.focus();
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            e.preventDefault();
            alert('メールアドレスの形式が正しくない行があります');
            input.focus();
            return;
        }
    }
});

// 新規ユーザーメール入力のイベントデリゲーション
document.getElementById('newUserRows').addEventListener('input', function(e) {
    if (e.target.name && e.target.name.includes('[email]')) {
        normalizeEmail(e.target);
        checkDuplicateEmail(e.target);
    }
});

// 背景クリックで閉じる
document.addEventListener('click', function(e) {
    const modal = document.getElementById('groupAddModal');
    if (e.target === modal) closeGroupAddModal();
});
</script>