<div id="user-internal" class="tab-pane list_body hidden">
    <!-- ヘッダーセクション -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">ユーザーリスト（内部）</h2>
            <p id="userCountInternal" class="text-sm text-gray-600 mt-1">ユーザ数：{{ count(array_filter($userList, fn($u) => (is_object($u) ? ($u->accountType ?? 'internal') : ($u['accountType'] ?? 'internal')) === 'internal')) }}</p>
            <p id="checkUserCountInternal" class="text-sm text-blue-600 mt-1">&nbsp;</p>
        </div>

        <!-- 通常モードのボタン群 -->
        <div id="buttonsUserInternal" class="flex flex-wrap gap-2">
            <button type="button" onclick="reloadWithHash('user-internal');"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
                <i class="fa fa-refresh mr-2" aria-hidden="true"></i> 更新
            </button>
            <button type="button" onclick="openUserModal('userAdd')"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                作成
            </button>
            <button type="button" onclick="editModeUserON('Internal')"
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
            <button type="button" onclick="downloadCSV('Internal')"
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
        <div id="exeEditUserInternal" class="flex gap-2" style="display: none;">
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
    <form id="userListInternal">
        <div class="bg-white rounded-lg shadow" style="overflow-x: auto;">
            <div style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="text-sm text-left" style="white-space: nowrap;">
                    <thead class="bg-gray-50 sticky top-0" style="z-index: 10;">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 50px;">#</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 50px;"><i class="fas fa-check"></i></th>
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
                        @php $internalIndex = 0; @endphp
                        @foreach($userList as $i => $user)
                        @php
                            $accountType = is_object($user) ? ($user->accountType ?? 'internal') : ($user['accountType'] ?? 'internal');
                        @endphp
                        @if($accountType === 'internal')
                        @php $internalIndex++; @endphp
                        <tr class="hover:bg-green-50 border-b">
                            <td class="px-4 py-2 text-gray-600">{{$internalIndex}}</td>
                            <td class="px-4 py-2">
                                @php $userId = is_object($user) ? $user->id : $user['id']; @endphp
                                <input type="checkbox" class="delUserTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500" name="userIds[]" value="{{$userId}}">
                            </td>
                            <td class="px-4 py-2">
                                @php $displayName = is_object($user) ? ($user->displayName ?? '') : ($user['displayName'] ?? ''); @endphp
                                <input type="text" name="userName{{$userId}}" value="{{$displayName}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php $kana = is_object($user) ? ($user->kana ?? '') : ($user['kana'] ?? ''); @endphp
                                <input type="text" name="userKana{{$userId}}" value="{{$kana}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php $emailAddress = is_object($user) ? ($user->emailAddress ?? '') : ($user['emailAddress'] ?? ''); @endphp
                                <input type="email" name="userEmail{{$userId}}" value="{{$emailAddress}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                <select disabled name="userTeamId{{$userId}}" class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                    @foreach($teamList as $team)
                                        @php
                                            $teamId = is_object($team) ? $team->id : $team['id'];
                                            $teamName = is_object($team) ? $team->name : $team['name'];
                                            $userTeamId = null;
                                            if (is_object($user)) {
                                                if (isset($user->team)) {
                                                    if (is_object($user->team)) { $userTeamId = $user->team->id; }
                                                    elseif (is_numeric($user->team)) { $userTeamId = $user->team; }
                                                }
                                            } else {
                                                if (isset($user['team']) && isset($user['team']['id'])) { $userTeamId = $user['team']['id']; }
                                            }
                                        @endphp
                                        <option value="{{$teamId}}" {{ $userTeamId == $teamId ? 'selected' : '' }}>{{$teamName}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <select name="userAccountType{{$userId}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                    <option value="internal" selected>内部</option>
                                    <option value="external">外部</option>
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $validFrom = is_object($user) ? ($user->validFrom ?? '') : ($user['validFrom'] ?? '');
                                    $validFromDate = $validFrom ? substr($validFrom, 0, 10) : '';
                                @endphp
                                <input type="date" name="userValidFrom{{$userId}}" value="{{$validFromDate}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $validTo = is_object($user) ? ($user->validTo ?? '') : ($user['validTo'] ?? '');
                                    $validToDate = $validTo ? substr($validTo, 0, 10) : '';
                                @endphp
                                <input type="date" name="userValidTo{{$userId}}" value="{{$validToDate}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php $description = is_object($user) ? ($user->description ?? '') : ($user['description'] ?? ''); @endphp
                                <textarea name="userDescription{{$userId}}" rows="1" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">{{$description}}</textarea>
                            </td>
                            <td class="px-4 py-2">
                                @php $authDescription = is_object($user) ? ($user->authDescription ?? '') : ($user['authDescription'] ?? ''); @endphp
                                <textarea name="userAuthDescription{{$userId}}" rows="1" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">{{$authDescription}}</textarea>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4 text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">保存</button>
        </div>
    </form>
</div>

{{-- 外部ユーザーリスト --}}
<div id="user-external" class="tab-pane list_body hidden">
    <!-- ヘッダーセクション -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">ユーザーリスト（外部）</h2>
            <p id="userCountExternal" class="text-sm text-gray-600 mt-1">ユーザ数：{{ count(array_filter($userList, fn($u) => (is_object($u) ? ($u->accountType ?? 'internal') : ($u['accountType'] ?? 'internal')) === 'external')) }}</p>
            <p id="checkUserCountExternal" class="text-sm text-blue-600 mt-1">&nbsp;</p>
        </div>

        <!-- 通常モードのボタン群 -->
        <div id="buttonsUserExternal" class="flex flex-wrap gap-2">
            <button type="button" onclick="reloadWithHash('user-external');"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
                <i class="fa fa-refresh mr-2" aria-hidden="true"></i> 更新
            </button>
            <button type="button" onclick="openUserModal('userAdd')"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                作成
            </button>
            <button type="button" onclick="editModeUserON('External')"
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
            <button type="button" onclick="downloadCSV('External')"
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
        <div id="exeEditUserExternal" class="flex gap-2" style="display: none;">
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
    <form id="userListExternal">
        <div class="bg-white rounded-lg shadow" style="overflow-x: auto;">
            <div style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="text-sm text-left" style="white-space: nowrap;">
                    <thead class="bg-gray-50 sticky top-0" style="z-index: 10;">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 50px;">#</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 50px;"><i class="fas fa-check"></i></th>
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
                        @php $externalIndex = 0; @endphp
                        @foreach($userList as $i => $user)
                        @php
                            $accountType = is_object($user) ? ($user->accountType ?? 'internal') : ($user['accountType'] ?? 'internal');
                        @endphp
                        @if($accountType === 'external')
                        @php $externalIndex++; @endphp
                        <tr class="hover:bg-green-50 border-b">
                            <td class="px-4 py-2 text-gray-600">{{$externalIndex}}</td>
                            <td class="px-4 py-2">
                                @php $userId = is_object($user) ? $user->id : $user['id']; @endphp
                                <input type="checkbox" class="delUserTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500" name="userIds[]" value="{{$userId}}">
                            </td>
                            <td class="px-4 py-2">
                                @php $displayName = is_object($user) ? ($user->displayName ?? '') : ($user['displayName'] ?? ''); @endphp
                                <input type="text" name="userName{{$userId}}" value="{{$displayName}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php $kana = is_object($user) ? ($user->kana ?? '') : ($user['kana'] ?? ''); @endphp
                                <input type="text" name="userKana{{$userId}}" value="{{$kana}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php $emailAddress = is_object($user) ? ($user->emailAddress ?? '') : ($user['emailAddress'] ?? ''); @endphp
                                <input type="email" name="userEmail{{$userId}}" value="{{$emailAddress}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                <select disabled name="userTeamId{{$userId}}" class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                    @foreach($teamList as $team)
                                        @php
                                            $teamId = is_object($team) ? $team->id : $team['id'];
                                            $teamName = is_object($team) ? $team->name : $team['name'];
                                            $userTeamId = null;
                                            if (is_object($user)) {
                                                if (isset($user->team)) {
                                                    if (is_object($user->team)) { $userTeamId = $user->team->id; }
                                                    elseif (is_numeric($user->team)) { $userTeamId = $user->team; }
                                                }
                                            } else {
                                                if (isset($user['team']) && isset($user['team']['id'])) { $userTeamId = $user['team']['id']; }
                                            }
                                        @endphp
                                        <option value="{{$teamId}}" {{ $userTeamId == $teamId ? 'selected' : '' }}>{{$teamName}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <select name="userAccountType{{$userId}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                    <option value="internal">内部</option>
                                    <option value="external" selected>外部</option>
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $validFrom = is_object($user) ? ($user->validFrom ?? '') : ($user['validFrom'] ?? '');
                                    $validFromDate = $validFrom ? substr($validFrom, 0, 10) : '';
                                @endphp
                                <input type="date" name="userValidFrom{{$userId}}" value="{{$validFromDate}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $validTo = is_object($user) ? ($user->validTo ?? '') : ($user['validTo'] ?? '');
                                    $validToDate = $validTo ? substr($validTo, 0, 10) : '';
                                @endphp
                                <input type="date" name="userValidTo{{$userId}}" value="{{$validToDate}}" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                @php $description = is_object($user) ? ($user->description ?? '') : ($user['description'] ?? ''); @endphp
                                <textarea name="userDescription{{$userId}}" rows="1" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">{{$description}}</textarea>
                            </td>
                            <td class="px-4 py-2">
                                @php $authDescription = is_object($user) ? ($user->authDescription ?? '') : ($user['authDescription'] ?? ''); @endphp
                                <textarea name="userAuthDescription{{$userId}}" rows="1" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">{{$authDescription}}</textarea>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4 text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">保存</button>
        </div>
    </form>
</div>

@include('main.modalUser')

<script>
window.addEventListener('load', function() {
    editModeUserOFF('Internal');
    editModeUserOFF('External');
});

function editModeUserON(suffix) {
    const buttonsUser = document.getElementById('buttonsUser' + suffix);
    if (buttonsUser) buttonsUser.style.setProperty('display', 'none', 'important');

    const exeEditUser = document.getElementById('exeEditUser' + suffix);
    if (exeEditUser) exeEditUser.style.display = 'flex';

    const form = document.getElementById('userList' + suffix);
    [...form.elements].forEach(e => {
        if (!e.classList.contains('delUserTarget')) {
            e.disabled = false;
            e.classList.remove('disabled:bg-gray-100');
            e.classList.add('bg-white');
        } else {
            e.disabled = true;
        }
    });
}

function editModeUserOFF(suffix) {
    const form = document.getElementById('userList' + suffix);
    if (form) {
        [...form.elements].forEach(e => {
            if (!e.classList.contains('delUserTarget')) {
                e.disabled = true;
                e.classList.add('disabled:bg-gray-100');
                e.classList.remove('bg-white');
            } else {
                e.disabled = false;
            }
        });
    }
    const buttonsUser = document.getElementById('buttonsUser' + suffix);
    const exeEditUser = document.getElementById('exeEditUser' + suffix);
    if (buttonsUser) buttonsUser.style.display = 'flex';
    if (exeEditUser) exeEditUser.style.display = 'none';
}

document.querySelectorAll('.delUserTarget').forEach(function(element) {
    element.addEventListener('change', function() {
        updateCheckCount();
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var inputs = document.querySelectorAll("input[type='text'], input[type='email'], input[type='date'], select, textarea");
    inputs.forEach(function(input) {
        input.addEventListener('change', function() {
            var checkbox = this.closest('tr').querySelector('input[type="checkbox"].delUserTarget');
            if (checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
});

function downloadTemplateCSV() {
    var url = './files/templateUsers.csv';
    var a = document.createElement('a');
    a.href = url;
    a.download = 'templateUsers.csv';
    a.style.display = 'none';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

const CSV_USER_HEADER = @json($csvUserHeaders);

function downloadCSV(suffix) {
    let csvData = [];
    csvData.push(CSV_USER_HEADER.join(','));

    const rows = document.querySelectorAll('#userList' + suffix + ' tr');
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = Array.from(row.cells).filter((_, index) => index > 1);
        const rowData = cells.map(cell => {
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
        const medilineId = row.querySelector('input[type="checkbox"].delUserTarget')?.value || '';
        rowData.push(medilineId);
        rowData.push('');
        csvData.push(rowData.join(','));
    }

    const csvString = csvData.join('\n');
    const link = document.createElement('a');
    const bom = '\uFEFF';
    const blob = new Blob([bom + csvString], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    link.href = url;
    link.download = 'user_list_' + suffix.toLowerCase() + '.csv';
    link.click();
    URL.revokeObjectURL(url);
}

// ============================================
// 無限スクロール
// ============================================
let currentPageInternal = 2;
let currentPageExternal = 2;
let isLoadingMoreInternal = false;
let isLoadingMoreExternal = false;
let hasMoreUsersInternal = true;
let hasMoreUsersExternal = true;

document.addEventListener('DOMContentLoaded', function() {
    // タブ表示時に画面が埋まっていなければ自動追加取得
    function checkAndLoadMore(suffix) {
        const sectionId = suffix === 'Internal' ? 'user-internal' : 'user-external';
        const sc = document.querySelector('#' + sectionId + ' .bg-white.rounded-lg.shadow > div');
        if (!sc) return;
        const hasMore = suffix === 'Internal' ? hasMoreUsersInternal : hasMoreUsersExternal;
        if (hasMore && sc.scrollHeight <= sc.clientHeight) {
            loadMoreUsers(suffix).then(function() {
                setTimeout(function() { checkAndLoadMore(suffix); }, 200);
            });
        }
    }
    // サイドバーのリンクをクリック時にチェック
    document.querySelectorAll('a[href="#user-internal"], a[href="#user-external"]').forEach(function(link) {
        link.addEventListener('click', function() {
            const suffix = this.getAttribute('href') === '#user-internal' ? 'Internal' : 'External';
            setTimeout(function() { checkAndLoadMore(suffix); }, 300);
        });
    });

    ['Internal', 'External'].forEach(suffix => {
        const sectionId = suffix === 'Internal' ? 'user-internal' : 'user-external';
        const scrollContainer = document.querySelector('#' + sectionId + ' .bg-white.rounded-lg.shadow > div');
        if (scrollContainer) {
            scrollContainer.addEventListener('scroll', function() {
                if (suffix === 'Internal') {
                    if (!isLoadingMoreInternal && hasMoreUsersInternal &&
                        scrollContainer.scrollHeight - scrollContainer.scrollTop <= scrollContainer.clientHeight + 100) {
                        loadMoreUsers('Internal');
                    }
                } else {
                    if (!isLoadingMoreExternal && hasMoreUsersExternal &&
                        scrollContainer.scrollHeight - scrollContainer.scrollTop <= scrollContainer.clientHeight + 100) {
                        loadMoreUsers('External');
                    }
                }
            });
        }
    });
});

function loadMoreUsers(suffix) {
    const isLoading = suffix === 'Internal' ? isLoadingMoreInternal : isLoadingMoreExternal;
    const hasMore = suffix === 'Internal' ? hasMoreUsersInternal : hasMoreUsersExternal;
    if (isLoading || !hasMore) return;

    if (suffix === 'Internal') { isLoadingMoreInternal = true; currentPageInternal++; }
    else { isLoadingMoreExternal = true; currentPageExternal++; }

    const page = suffix === 'Internal' ? currentPageInternal : currentPageExternal;
    const accountType = suffix === 'Internal' ? 'internal' : 'external';

    showLoadingIndicator(suffix);

    fetch('/api/users/paginated?page=' + page + '&accountType=' + accountType)
        .then(response => response.json())
        .then(data => {
            if (data.users && data.users.length > 0) {
                appendUsersToTable(data.users, suffix);
                if (suffix === 'Internal') hasMoreUsersInternal = data.hasMore;
                else hasMoreUsersExternal = data.hasMore;
                updateUserCount(suffix);
            } else {
                if (suffix === 'Internal') hasMoreUsersInternal = false;
                else hasMoreUsersExternal = false;
            }
            if (suffix === 'Internal') isLoadingMoreInternal = false;
            else isLoadingMoreExternal = false;
            hideLoadingIndicator(suffix);
        })
        .catch(error => {
            console.error('Failed to load users:', error);
            if (suffix === 'Internal') isLoadingMoreInternal = false;
            else isLoadingMoreExternal = false;
            hideLoadingIndicator(suffix);
        });
}

function appendUsersToTable(users, suffix) {
    const tbody = document.querySelector('#userList' + suffix + ' tbody');
    const teamList = @json($teamList);

    users.forEach((user, index) => {
        const currentRowCount = tbody.children.length;
        const rowNumber = currentRowCount + index + 1;
        const tr = createUserRow(user, rowNumber, teamList);
        tbody.appendChild(tr);

        const checkbox = tr.querySelector('.delUserTarget');
        if (checkbox) checkbox.addEventListener('change', updateCheckCount);

        const inputs = tr.querySelectorAll("input[type='text'], input[type='email'], input[type='date'], select, textarea");
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                const cb = this.closest('tr').querySelector('input[type="checkbox"].delUserTarget');
                if (cb) { cb.checked = true; cb.dispatchEvent(new Event('change')); }
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

    let teamOptions = '';
    teamList.forEach(team => {
        const teamId = team.id || team['id'];
        const teamName = team.name || team['name'];
        const userTeamId = user.team?.id || user.teamId;
        const selected = userTeamId == teamId ? 'selected' : '';
        teamOptions += '<option value="' + teamId + '" ' + selected + '>' + teamName + '</option>';
    });

    const internalSelected = accountType === 'internal' ? 'selected' : '';
    const externalSelected = accountType === 'external' ? 'selected' : '';

    tr.innerHTML =
        '<td class="px-4 py-2 text-gray-600">' + rowNumber + '</td>' +
        '<td class="px-4 py-2"><input type="checkbox" class="delUserTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500" name="userIds[]" value="' + userId + '"></td>' +
        '<td class="px-4 py-2"><input type="text" name="userName' + userId + '" value="' + displayName + '" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" /></td>' +
        '<td class="px-4 py-2"><input type="text" name="userKana' + userId + '" value="' + kana + '" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" /></td>' +
        '<td class="px-4 py-2"><input type="email" name="userEmail' + userId + '" value="' + emailAddress + '" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" /></td>' +
        '<td class="px-4 py-2"><select disabled name="userTeamId' + userId + '" class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">' + teamOptions + '</select></td>' +
        '<td class="px-4 py-2"><select name="userAccountType' + userId + '" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100"><option value="internal" ' + internalSelected + '>内部</option><option value="external" ' + externalSelected + '>外部</option></select></td>' +
        '<td class="px-4 py-2"><input type="date" name="userValidFrom' + userId + '" value="' + validFrom + '" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" /></td>' +
        '<td class="px-4 py-2"><input type="date" name="userValidTo' + userId + '" value="' + validTo + '" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" /></td>' +
        '<td class="px-4 py-2"><textarea name="userDescription' + userId + '" rows="1" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">' + description + '</textarea></td>' +
        '<td class="px-4 py-2"><textarea name="userAuthDescription' + userId + '" rows="1" disabled class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">' + authDescription + '</textarea></td>';

    return tr;
}

function updateCheckCount() {
    const checkboxes = document.getElementsByClassName('delUserTarget');
    let count = 0;
    for (let i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) count++;
    }
    ['Internal', 'External'].forEach(suffix => {
        const count_text = document.getElementById('checkUserCount' + suffix);
        if (count_text) count_text.innerHTML = count > 0 ? 'チェック数：' + count : '&nbsp;';
    });
}

function updateUserCount(suffix) {
    const userCount = document.getElementById('userCount' + suffix);
    const totalCount = document.querySelectorAll('#userList' + suffix + ' tbody tr').length;
    if (userCount) userCount.textContent = 'ユーザ数：' + totalCount;
}

function showLoadingIndicator(suffix) {
    const sectionId = suffix === 'Internal' ? 'user-internal' : 'user-external';
    const existingIndicator = document.getElementById('loading-indicator-' + suffix);
    if (existingIndicator) return;
    const indicator = document.createElement('div');
    indicator.id = 'loading-indicator-' + suffix;
    indicator.className = 'text-center py-4';
    indicator.innerHTML = '<span class="text-gray-500">読み込み中...</span>';
    const tableContainer = document.querySelector('#' + sectionId + ' .bg-white.rounded-lg.shadow > div');
    if (tableContainer) tableContainer.appendChild(indicator);
}

function hideLoadingIndicator(suffix) {
    const indicator = document.getElementById('loading-indicator-' + suffix);
    if (indicator) indicator.remove();
}
</script>