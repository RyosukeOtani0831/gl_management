<div id="user" class="tab-pane list_body hidden">
    <!-- ヘッダーセクション -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">ユーザーリスト</h2>
            <p id="userCount" class="text-sm text-gray-600 mt-1">ユーザ数：{{ count($userList) }}</p>
            <p id="checkUserCount" class="text-sm text-blue-600 mt-1">&nbsp;</p>
        </div>
        
        @include('main.modalUser')
        
        <!-- 通常モードのボタン群 -->
        <div id="buttonsUser" class="flex flex-wrap gap-2">
            <button type="button" onclick="reloadWithHash('user');" 
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
                <i class="fa fa-refresh mr-2" aria-hidden="true"></i> 更新
            </button>
            <button type="button" onclick="openUserModal('userAdd')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                作成
            </button>
            <button type="button" onclick="editModeUserON()" 
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                編集
            </button>
            <button type="button" onclick="openUserModal('userDel')" 
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                削除
            </button>
            <button type="button" onclick="downloadTemplateCSV()" 
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
                テンプレートDL
            </button>
            <button type="button" onclick="downloadCSV()" 
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
                リストDL
            </button>
            <button type="button" onclick="openUserModal('userCsvAdd')" 
                    class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500 transition">
                一括追加
            </button>
            <button type="button" onclick="openUserModal('userCsvEdit')" 
                    class="bg-orange-400 text-white px-4 py-2 rounded hover:bg-orange-500 transition">
                一括編集
            </button>
            <button type="button" onclick="openUserModal('userCsvDel')" 
                    class="bg-orange-400 text-white px-4 py-2 rounded hover:bg-orange-500 transition">
                一括削除
            </button>
        </div>
        
        <!-- 編集モード用のボタン群 -->
        <div id="exeEditUser" class="flex gap-2" style="display: none;">
            <button type="button" onclick="openUserModal('userEdit')" 
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                確定
            </button>
            <button type="button" onclick="openUserModal('userEditCancel')" 
                    class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                キャンセル
            </button>
        </div>
    </div>

    <!-- テーブルセクション -->
    <form id="userList">
        <div class="bg-white rounded-lg shadow" style="overflow-x: auto;">
            <div style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="text-sm text-left" style="white-space: nowrap;">
                    <thead class="bg-gray-50 sticky top-0" style="z-index: 10;">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 50px;">#</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 50px;">
                                <i class="fas fa-check"></i>
                            </th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 150px;">氏名</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 150px;">カナ</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 200px;">メール</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 150px;">チーム</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">アカウント種別</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">利用開始日</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">利用終了日</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">ユーザーコメント</th>
                            <th class="px-4 py-3 font-semibold text-gray-700">管理者コメント</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userList as $i => $user)
                        <tr class="hover:bg-green-50 border-b">
                            <td class="px-4 py-2 text-gray-600">{{$i+1}}</td>
                            <td class="px-4 py-2">
                                @php
                                    $userId = is_object($user) ? $user->id : $user['id'];
                                @endphp
                                <input type="checkbox" class="delUserTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                                       name="userIds[]" value="{{$userId}}">
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $displayName = is_object($user) ? ($user->displayName ?? '') : ($user['displayName'] ?? '');
                                @endphp
                                <input type="text" name="userName{{$userId}}" value="{{$displayName}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $kana = is_object($user) ? ($user->kana ?? '') : ($user['kana'] ?? '');
                                @endphp
                                <input type="text" name="userKana{{$userId}}" value="{{$kana}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $emailAddress = is_object($user) ? ($user->emailAddress ?? '') : ($user['emailAddress'] ?? '');
                                @endphp
                                <input type="email" name="userEmail{{$userId}}" value="{{$emailAddress}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                <select disabled name="userTeamId{{$userId}}"
                                        class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                    @foreach($teamList as $team)
                                        @php
                                            $teamId = is_object($team) ? $team->id : $team['id'];
                                            $teamName = is_object($team) ? $team->name : $team['name'];
                                            
                                            // ユーザーのチーム情報を取得
                                            $userTeamId = null;
                                            if (is_object($user)) {
                                                if (isset($user->team)) {
                                                    if (is_object($user->team)) {
                                                        $userTeamId = $user->team->id;
                                                    } elseif (is_numeric($user->team)) {
                                                        $userTeamId = $user->team;
                                                    }
                                                }
                                            } else {
                                                if (isset($user['team']) && isset($user['team']['id'])) {
                                                    $userTeamId = $user['team']['id'];
                                                }
                                            }
                                        @endphp
                                        @if($userTeamId && $userTeamId == $teamId)
                                            <option value="{{$teamId}}" selected>{{$teamName}}</option>
                                        @else
                                            <option value="{{$teamId}}">{{$teamName}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $accountType = is_object($user) ? ($user->accountType ?? 'internal') : ($user['accountType'] ?? 'internal');
                                @endphp
                                <select name="userAccountType{{$userId}}" disabled
                                        class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                    <option value="internal" {{ $accountType === 'internal' ? 'selected' : '' }}>内部</option>
                                    <option value="external" {{ $accountType === 'external' ? 'selected' : '' }}>外部</option>
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $validFrom = is_object($user) ? ($user->validFrom ?? '') : ($user['validFrom'] ?? '');
                                    $validFromDate = $validFrom ? substr($validFrom, 0, 10) : '';
                                @endphp
                                <input type="date" name="userValidFrom{{$userId}}" value="{{$validFromDate}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $validTo = is_object($user) ? ($user->validTo ?? '') : ($user['validTo'] ?? '');
                                    $validToDate = $validTo ? substr($validTo, 0, 10) : '';
                                @endphp
                                <input type="date" name="userValidTo{{$userId}}" value="{{$validToDate}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $description = is_object($user) ? ($user->description ?? '') : ($user['description'] ?? '');
                                @endphp
                                <textarea name="userDescription{{$userId}}" rows="1" disabled
                                          class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">{{$description}}</textarea>
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $authDescription = is_object($user) ? ($user->authDescription ?? '') : ($user['authDescription'] ?? '');
                                @endphp
                                <textarea name="userAuthDescription{{$userId}}" rows="1" disabled
                                          class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">{{$authDescription}}</textarea>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                保存
            </button>
        </div>
    </form>
</div>

<script>
window.addEventListener('load', function() {
    editModeUserOFF();
});

function editModeUserON() {
    // ボタン群（作成、編集、削除など）を非表示にする
    const buttonsUser = document.getElementById('buttonsUser');
    if (buttonsUser) {
        buttonsUser.style.setProperty('display', 'none', 'important');
    }

    // 確定ボタンとキャンセルボタンを表示
    const exeEditUser = document.getElementById('exeEditUser');
    if (exeEditUser) {
        exeEditUser.style.display = "flex";
    }

    // フォームの入力を有効にする（チェックボックスを除く）
    const form = document.getElementById('userList');
    [...form.elements].forEach(e => {
        if (!e.classList.contains("delUserTarget")) {
            e.disabled = false;
            e.classList.remove('disabled:bg-gray-100');
            e.classList.add('bg-white');
        } else {
            e.disabled = true;
        }
    });
}

function editModeUserOFF() {
    const form = document.getElementById('userList');
    if (form) {
        [...form.elements].forEach(e => {
            if (!e.classList.contains("delUserTarget")) {
                e.disabled = true;
                e.classList.add('disabled:bg-gray-100');
                e.classList.remove('bg-white');
            } else {
                e.disabled = false;
            }
        });
    }

    const buttonsUser = document.getElementById('buttonsUser');
    const exeEditUser = document.getElementById('exeEditUser');

    if (buttonsUser) {
        buttonsUser.style.display = "flex";
    }

    if (exeEditUser) {
        exeEditUser.style.display = "none";
    }
}

// チェックボックスのカウント更新
document.querySelectorAll('.delUserTarget').forEach(function(element){
    element.addEventListener('change', function(click_element){
        updateCheckCount();
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // 全てのinput要素を取得
    var inputs = document.querySelectorAll("input[type='text'], input[type='email'], input[type='date'], select, textarea");

    // 各input要素にイベントリスナーを追加
    inputs.forEach(function(input) {
        input.addEventListener("change", function() {
            // 対応するチェックボックスを取得
            var checkbox = this.closest("tr").querySelector("input[type='checkbox'].delUserTarget");

            // チェックボックスをONにする
            if (checkbox) {
                checkbox.checked = true;
                // カウントを更新
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
});

function downloadTemplateCSV() {
    // ダウンロードファイルのURLを指定
    var url = './files/templateUsers.csv';
    
    // aタグを仮に作成してダウンロードを実行
    var a = document.createElement('a');
    a.href = url;
    a.download = 'templateUsers.csv';
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// PHPから受け取ったヘッダー情報をJavaScript用に変換
const CSV_USER_HEADER = @json($csvUserHeaders);

function downloadCSV() {
    // CSV用のデータを格納する配列
    let csvData = [];

    // ヘッダー情報をCSVに追加
    csvData.push(CSV_USER_HEADER.join(','));

    // ユーザーリストの行（2行目以降）を取得
    const rows = document.querySelectorAll("#userList tr");

    // 2行目以降の行データを処理
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];

        // **1列目（順番 ID）と2列目（チェックボックス）はスキップ**  
        const cells = Array.from(row.cells).filter((_, index) => index > 1);

        // 各セルのデータを取得して配列に格納
        const rowData = cells.map(cell => {
            // セル内の入力フィールドまたはテキストを取得
            const input = cell.querySelector('input[type="text"], input[type="email"], input[type="date"], select, textarea');
            if (input) {
                if (input.tagName.toLowerCase() === 'select') {
                    const selectedOption = input.options[input.selectedIndex];
                    return selectedOption ? selectedOption.text : '';
                }
                return input.value;
            }
            return cell.innerText.trim();
        });

        // 各行に「medilineID」と「削除フラグ」を追加
        const medilineId = row.querySelector('input[type="checkbox"].delUserTarget')?.value || '';
        rowData.push(medilineId);
        rowData.push('');

        // 行データをCSVに追加
        csvData.push(rowData.join(','));
    }

    // CSVデータを文字列として結合
    const csvString = csvData.join("\n");

    // ダウンロード用リンクを作成
    const link = document.createElement('a');

    // UTF-8 BOMを追加してCSVを作成
    const bom = '\uFEFF';
    const blob = new Blob([bom + csvString], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    // ダウンロードリンク設定
    link.href = url;
    link.download = 'user_list.csv';

    // ダウンロード実行
    link.click();
    URL.revokeObjectURL(url);
}

// ============================================
// 無限スクロール実装
// ============================================
let currentPage = 2;
let isLoadingMore = false;
let hasMoreUsers = true;
let allLoadedUsers = []; // 全ユーザーデータを保持

// スクロールイベントリスナー
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.querySelector('#user .bg-white.rounded-lg.shadow > div');
    
    if (scrollContainer) {
        scrollContainer.addEventListener('scroll', function() {
            if (isLoadingMore || !hasMoreUsers) return;
            
            // 下から100px以内でトリガー
            if (scrollContainer.scrollHeight - scrollContainer.scrollTop <= scrollContainer.clientHeight + 100) {
                loadMoreUsers();
            }
        });
    }
});

function loadMoreUsers() {
    if (isLoadingMore || !hasMoreUsers) return;
    
    isLoadingMore = true;
    currentPage++;
    
    // ローディング表示
    showLoadingIndicator();
    
    fetch(`/api/users/paginated?page=${currentPage}`)
        .then(response => response.json())
        .then(data => {
            if (data.users && data.users.length > 0) {
                appendUsersToTable(data.users);
                allLoadedUsers = allLoadedUsers.concat(data.users);
                hasMoreUsers = data.hasMore;
                
                // ユーザー数更新
                updateUserCount();
            } else {
                hasMoreUsers = false;
            }
            
            isLoadingMore = false;
            hideLoadingIndicator();
        })
        .catch(error => {
            console.error('Failed to load users:', error);
            isLoadingMore = false;
            hideLoadingIndicator();
        });
}

function appendUsersToTable(users) {
    const tbody = document.querySelector('#userList tbody');
    const teamList = @json($teamList); // PHPから取得
    
    users.forEach((user, index) => {
        const currentRowCount = tbody.children.length;
        const rowNumber = currentRowCount + index + 1;
        
        const tr = createUserRow(user, rowNumber, teamList);
        tbody.appendChild(tr);
        
        // チェックボックスにイベントリスナー追加
        const checkbox = tr.querySelector('.delUserTarget');
        if (checkbox) {
            checkbox.addEventListener('change', updateCheckCount);
        }
        
        // 入力フィールドにイベントリスナー追加（編集時の自動チェック）
        const inputs = tr.querySelectorAll("input[type='text'], input[type='email'], input[type='date'], select, textarea");
        inputs.forEach(input => {
            input.addEventListener("change", function() {
                const checkbox = this.closest("tr").querySelector("input[type='checkbox'].delUserTarget");
                if (checkbox) {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        });
    });
}

function createUserRow(user, rowNumber, teamList) {
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-green-50 border-b';
    
    const userId = user.id;
    const displayName = user.displayName || '';
    const kana = user.kana || '';
    const emailAddress = user.emailAddress || '';
    const validFrom = user.validFrom ? user.validFrom.substring(0, 10) : '';
    const validTo = user.validTo ? user.validTo.substring(0, 10) : '';
    const description = user.description || '';
    const authDescription = user.authDescription || '';
    const accountType = user.accountType || 'internal';
    
    // チームセレクトボックスの生成
    let teamOptions = '';
    teamList.forEach(team => {
        const teamId = team.id || team['id'];
        const teamName = team.name || team['name'];
        const userTeamId = user.team?.id || user.teamId;
        const selected = userTeamId == teamId ? 'selected' : '';
        teamOptions += `<option value="${teamId}" ${selected}>${teamName}</option>`;
    });
    
    tr.innerHTML = `
        <td class="px-4 py-2 text-gray-600">${rowNumber}</td>
        <td class="px-4 py-2">
            <input type="checkbox" class="delUserTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                   name="userIds[]" value="${userId}">
        </td>
        <td class="px-4 py-2">
            <input type="text" name="userName${userId}" value="${displayName}"
                   disabled
                   class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
        </td>
        <td class="px-4 py-2">
            <input type="text" name="userKana${userId}" value="${kana}"
                   disabled
                   class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
        </td>
        <td class="px-4 py-2">
            <input type="email" name="userEmail${userId}" value="${emailAddress}"
                   disabled
                   class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
        </td>
        <td class="px-4 py-2">
            <select disabled name="userTeamId${userId}"
                    class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                ${teamOptions}
            </select>
        </td>
        <td class="px-4 py-2">
            <select name="userAccountType${userId}" disabled
                    class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                <option value="internal" ${accountType === 'internal' ? 'selected' : ''}>内部</option>
                <option value="external" ${accountType === 'external' ? 'selected' : ''}>外部</option>
            </select>
        </td>
        <td class="px-4 py-2">
            <input type="date" name="userValidFrom${userId}" value="${validFrom}"
                   disabled
                   class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
        </td>
        <td class="px-4 py-2">
            <input type="date" name="userValidTo${userId}" value="${validTo}"
                   disabled
                   class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
        </td>
        <td class="px-4 py-2">
            <textarea name="userDescription${userId}" rows="1" disabled
                      class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">${description}</textarea>
        </td>
        <td class="px-4 py-2">
            <textarea name="userAuthDescription${userId}" rows="1" disabled
                      class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">${authDescription}</textarea>
        </td>
    `;
    
    return tr;
}

function updateCheckCount() {
    const checkboxes = document.getElementsByClassName("delUserTarget");
    let count = 0;
    for (let i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            count++;
        }
    }
    
    const count_text = document.getElementById('checkUserCount');
    count_text.innerHTML = count > 0 ? "チェック数：" + count : "&nbsp;";
}

function updateUserCount() {
    const userCount = document.getElementById('userCount');
    const totalCount = document.querySelectorAll('#userList tbody tr').length;
    userCount.textContent = `ユーザ数：${totalCount}`;
}

function showLoadingIndicator() {
    const existingIndicator = document.getElementById('loading-indicator');
    if (existingIndicator) return;
    
    const indicator = document.createElement('div');
    indicator.id = 'loading-indicator';
    indicator.className = 'text-center py-4';
    indicator.innerHTML = '<span class="text-gray-500">読み込み中...</span>';
    
    const tableContainer = document.querySelector('#user .bg-white.rounded-lg.shadow > div');
    if (tableContainer) {
        tableContainer.appendChild(indicator);
    }
}

function hideLoadingIndicator() {
    const indicator = document.getElementById('loading-indicator');
    if (indicator) {
        indicator.remove();
    }
}

</script>