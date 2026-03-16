<!-- ヘッダーセクション -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-gray-900">ケースリスト</h2>
        <p class="text-sm text-gray-600 mt-1">ケース数：{{ count($groupList) }}</p>
    </div>
    
    <div class="flex gap-2">
        <button type="button" onclick="reloadGroup();" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
            <i class="fa fa-refresh mr-2"></i> 更新
        </button>
        <button type="button" onclick="openAddGroupModal()" 
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition flex items-center">
            <i class="fa fa-plus mr-2"></i> 新規追加
        </button>
    </div>
</div>

<!-- グループリストテーブル（軽量版） -->
<div class="bg-white rounded-lg shadow border overflow-hidden">
    <div style="max-height: calc(100vh - 280px); overflow-y: auto;">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-50 sticky top-0" style="z-index: 10;">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Group Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Team</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">ステータス</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">メンバー</th>
                    <th class="px-6 py-3 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($groupList as $i => $group)
                    @php
                        $adminCount = 0;
                        $memberCount = 0;
                        
                        foreach($group['users'] as $user) {
                            if($user['isAdmin']) {
                                $adminCount++;
                            }
                            $memberCount++;
                        }
                        
                        // チーム名を取得（teams配列から取得）
                        $teamName = '';
                        $teamId = null;
                        
                        // まずteams配列をチェック
                        if (isset($group['teams']) && !empty($group['teams'])) {
                            $teamId = $group['teams'][0]['id'];
                            $teamName = $group['teams'][0]['name'];
                        }
                        // teams配列がない場合はteamIdから検索
                        elseif (isset($group['teamId'])) {
                            $teamId = $group['teamId'];
                            foreach($teamList as $team) {
                                if($team['id'] === $teamId) {
                                    $teamName = $team['name'];
                                    break;
                                }
                            }
                        }
                    @endphp
                    <tr class="group hover:bg-blue-50 cursor-pointer transition-colors duration-150" 
                        onclick="openDrawer({{ $group['id'] }}, '{{ addslashes($group['name']) }}', {{ $group['public'] ? 'true' : 'false' }}, '{{ addslashes($group['avatarFileId'] ?? '') }}', {{ $teamId ?? 'null' }}, {{ !empty($group['isClosed']) ? 'true' : 'false' }})">
                        <td class="px-6 py-4 text-gray-600">{{ $i + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 group-hover:text-blue-700">
                            {{ $group['name'] }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $teamName ?: '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if(!empty($group['isClosed']))
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fa fa-check mr-1"></i>クローズ
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            @if($groupPermissionInfo['hideGroupPermission'])
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fa fa-users mr-1"></i> {{ $memberCount }}
                                </span>
                            @else
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-1">
                                    <i class="fa fa-user-shield mr-1"></i> {{ $adminCount }}
                                </span>
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fa fa-users mr-1"></i> {{ $memberCount }}
                                </span>
                            @endif
                        </td>                        <td class="px-6 py-4 text-right text-gray-300 group-hover:text-blue-500">
                            <i class="fa fa-chevron-right"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- バックドロップ（背景） -->
<div id="drawer-backdrop" 
     onclick="closeDrawer()" 
     class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm z-40 opacity-0 invisible transition-all duration-300">
</div>

<!-- ドロワー（右側パネル） -->
<div id="drawer-panel" 
     class="fixed top-0 right-0 h-full w-[480px] bg-white shadow-2xl z-50 transform translate-x-full flex flex-col border-l border-gray-100 transition-transform duration-300 ease-in-out">
    
    <!-- ヘッダー -->
    <div class="px-6 py-5 border-b bg-gray-50 flex justify-between items-start">
        <div class="flex-1 mr-4">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Group Details</span>
            <div class="flex items-center gap-2 mt-1">
                <input type="text" id="drawer-group-name" 
                       class="text-2xl font-bold text-gray-800 border-0 p-0 focus:ring-0 bg-transparent w-full" 
                       readonly>
            </div>
            <div class="mt-2 space-y-2">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">チーム</label>
                    <select id="drawer-group-team" disabled
                            class="w-full border rounded p-2 text-sm bg-gray-50">
                        @foreach($teamList as $team)
                            <option value="{{ $team['id'] }}">{{ $team['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- 公開/非公開は外来Lawでは強制非公開のため非表示 --}}
                <input type="hidden" id="drawer-group-public" value="0">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">ステータス</label>
                    <button type="button" id="drawer-group-close-btn" onclick="toggleCaseClose()"
                            class="px-3 py-1 rounded text-sm font-medium transition">
                    </button>
                </div>
            </div>
        </div>
        <button onclick="closeDrawer()" 
                class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-200 rounded-full transition">
            <i class="fa fa-times text-xl"></i>
        </button>
    </div>

    <!-- 編集モード切替 -->
    <div class="px-6 py-3 bg-gray-50 border-b flex justify-between items-center">
        <div id="drawer-view-mode" class="flex gap-2">
            <button onclick="enableDrawerEditMode()" 
                    class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 transition">
                <i class="fa fa-edit mr-1"></i> 編集
            </button>
            <button onclick="confirmDeleteGroup()" 
                    class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700 transition">
                <i class="fa fa-trash mr-1"></i> 削除
            </button>
        </div>
        <div id="drawer-edit-mode" class="flex gap-2" style="display: none;">
            <button onclick="saveGroupChanges()" 
                    class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 transition">
                <i class="fa fa-save mr-1"></i> 保存
            </button>
            <button onclick="cancelDrawerEdit()" 
                    class="bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700 transition">
                キャンセル
            </button>
        </div>
    </div>

    <!-- メンバーリスト -->
    <div class="flex-1 overflow-y-auto">
        <!-- メンバー追加エリア（編集モード時のみ表示） -->
        <div id="drawer-add-member-area" class="sticky top-0 bg-white border-b z-10 shadow-sm" style="display: none;">
            <div class="p-4 border-b bg-gray-50">
                <div class="flex gap-2 items-center">
                    <div class="relative flex-1">
                        <i class="fa fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" id="member-search-input" 
                               placeholder="ユーザーを絞り込み..." 
                               class="w-full pl-9 pr-3 py-2 border rounded bg-white focus:ring-2 focus:ring-green-500 outline-none text-sm transition"
                               oninput="filterUserList(this.value)">
                    </div>
                    <span id="selected-count" class="text-sm text-gray-500 whitespace-nowrap">0件選択</span>
                </div>
                <div class="flex gap-2 mt-3">
                    @if($groupPermissionInfo['hideGroupPermission'])
                        <button onclick="addSelectedUsers(false)" 
                                class="flex-1 text-sm px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                            <i class="fa fa-user-plus mr-1"></i> 追加
                        </button>
                    @else
                        <button onclick="addSelectedUsers(true)" 
                                class="flex-1 text-sm px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            <i class="fa fa-user-shield mr-1"></i> 管理者として追加
                        </button>
                        <button onclick="addSelectedUsers(false)" 
                                class="flex-1 text-sm px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                            <i class="fa fa-user mr-1"></i> メンバーとして追加
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- ユーザー一覧（チェックボックス付き） -->
            <div id="user-select-list" class="max-h-64 overflow-y-auto bg-white">
                <!-- ここにユーザー一覧が表示される -->
            </div>
        </div>

        <!-- メンバーリスト本体 -->
        <div id="drawer-members-list" class="divide-y divide-gray-100">
            <div class="flex items-center justify-center py-12 text-gray-400">
                <i class="fa fa-spinner fa-spin text-2xl"></i>
            </div>
        </div>
    </div>
</div>

@include('main/modalGroup')

<script>
let currentGroupId = null;
let currentGroupData = null;
let currentMembers = []; // { ...user, isAdmin: true/false } の形式
let allUsers = [];
let isEditMode = false;
let selectedUserIds = []; // 選択されたユーザーID

// 更新処理（ドロワーを閉じてから更新）
function reloadGroup() {
    closeDrawer();
    setTimeout(() => {
        reloadWithHash('group');
    }, 100);
}

// クローズボタンの表示を更新
function updateCloseBtn(isClosed) {
    const btn = document.getElementById('drawer-group-close-btn');
    if (!btn) return;
    if (isClosed) {
        btn.textContent = 'ケースを再開する';
        btn.className = 'px-3 py-1 rounded text-sm font-medium transition bg-green-100 text-green-800 hover:bg-green-200';
    } else {
        btn.textContent = 'クローズする';
        btn.className = 'px-3 py-1 rounded text-sm font-medium transition bg-red-100 text-red-800 hover:bg-red-200';
    }
}

// クローズ状態をトグル
function toggleCaseClose() {
    const newIsClosed = !currentGroupData.isClosed;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/group/close';
    form.innerHTML = `
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="groupId" value="${currentGroupId}">
        <input type="hidden" name="isClosed" value="${newIsClosed ? 1 : 0}">
    `;
    document.body.appendChild(form);
    form.submit();
}

// ドロワーを開く
function openDrawer(groupId, groupName, isPublic, avatarFileId, teamId, isClosed) {
    currentGroupId = groupId;
    isEditMode = false;
    selectedUserIds = [];
    
    // 基本情報をセット
    document.getElementById('drawer-group-name').value = groupName;
    if (teamId !== null) {
        document.getElementById('drawer-group-team').value = teamId;
    } else {
        document.getElementById('drawer-group-team').selectedIndex = 0;
    }
    
    currentGroupData = {
        id: groupId,
        name: groupName,
        public: isPublic,
        avatarFileId: avatarFileId,
        teamId: teamId,
        isClosed: isClosed
    };
    // クローズボタンの表示更新
    updateCloseBtn(isClosed);
    
    // 編集モードをリセット
    document.getElementById('drawer-view-mode').style.display = 'flex';
    document.getElementById('drawer-edit-mode').style.display = 'none';
    document.getElementById('drawer-add-member-area').style.display = 'none';
    document.getElementById('drawer-group-name').readOnly = true;
    document.getElementById('drawer-group-team').disabled = true;
    
    // メンバーリストをローディング表示
    document.getElementById('drawer-members-list').innerHTML = `
        <div class="flex items-center justify-center py-12 text-gray-400">
            <i class="fa fa-spinner fa-spin text-2xl"></i>
        </div>
    `;
    
    // ドロワーを表示
    document.getElementById('drawer-backdrop').classList.remove('opacity-0', 'invisible');
    document.getElementById('drawer-panel').classList.remove('translate-x-full');
    
    // メンバー情報を取得
    loadGroupMembers(groupId);
}

// ドロワーを閉じる
function closeDrawer() {
    document.getElementById('drawer-panel').classList.add('translate-x-full');
    document.getElementById('drawer-backdrop').classList.add('opacity-0', 'invisible');
    currentGroupId = null;
    currentGroupData = null;
    currentMembers = [];
    allUsers = [];
    isEditMode = false;
    selectedUserIds = [];
}

// グループメンバーを読み込み
function loadGroupMembers(groupId) {
    fetch(`/api/group/${groupId}/admin-members`)
        .then(response => response.json())
        .then(data => {
            console.log('Loaded members:', data.members);
            
            // メンバーにはすでにisAdminフラグが付いている
            currentMembers = data.members.map(m => {
                console.log('Member:', m.displayName, 'isAdmin:', m.isAdmin);
                return {
                    id: m.id,
                    displayName: m.displayName,
                    emailAddress: m.emailAddress || '',
                    kana: m.kana || '',
                    isAdmin: m.isAdmin === true || m.isAdmin === 1  // 厳密にチェック
                };
            });
            
            allUsers = data.allUsers;
            console.log('Current members after mapping:', currentMembers);
            renderMembers();
        })
        .catch(error => {
            console.error('Error loading members:', error);
            document.getElementById('drawer-members-list').innerHTML = `
                <div class="flex items-center justify-center py-12 text-red-500">
                    <i class="fa fa-exclamation-triangle mr-2"></i> 読み込みエラー
                </div>
            `;
        });
}

// メンバーリストを描画
function renderMembers() {
    const listEl = document.getElementById('drawer-members-list');
    const hideGroupPermission = {{ $groupPermissionInfo['hideGroupPermission'] ? 'true' : 'false' }};
    
    if (currentMembers.length === 0) {
        listEl.innerHTML = `
            <div class="flex items-center justify-center py-12 text-gray-400">
                <i class="fa fa-users mr-2"></i> メンバーがいません
            </div>
        `;
        return;
    }
    
    let html = '';
    currentMembers.forEach(member => {
        const adminBadge = member.isAdmin 
            ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium"><i class="fa fa-user-shield mr-1"></i>管理者</span>'
            : '<span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-medium"><i class="fa fa-user mr-1"></i>メンバー</span>';
        
        // hideGroupPermission時は権限変更ボタンを非表示
        const roleToggle = (isEditMode && !hideGroupPermission) ? `
            <button onclick="toggleMemberRole(${member.id}); event.stopPropagation();" 
                    class="text-xs px-2 py-1 rounded border border-gray-300 hover:bg-gray-100 transition mr-2">
                権限変更
            </button>
        ` : '';
        
        const deleteBtn = isEditMode ? `
            <button onclick="removeMember(${member.id}); event.stopPropagation();" 
                    class="text-gray-300 hover:text-red-500 p-2 rounded hover:bg-red-50 transition">
                <i class="fa fa-trash"></i>
            </button>
        ` : '';
        
        // hideGroupPermission時は管理者バッジを非表示
        const badgeDisplay = hideGroupPermission ? '' : adminBadge;
        
        html += `
            <div class="px-6 py-3 hover:bg-blue-50 flex items-center justify-between transition-colors">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-bold text-gray-600">
                        ${member.displayName.substring(0, 2)}
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-bold text-gray-900">${member.displayName}</div>
                        <div class="text-xs text-gray-500">${member.emailAddress || ''}</div>
                    </div>
                    ${badgeDisplay}
                </div>
                <div class="flex items-center gap-2">
                    ${roleToggle}
                    ${deleteBtn}
                </div>
            </div>
        `;
    });
    
    listEl.innerHTML = html;
}

// 編集モードを有効化
function enableDrawerEditMode() {
    isEditMode = true;
    selectedUserIds = []; // 選択をリセット
    document.getElementById('drawer-view-mode').style.display = 'none';
    document.getElementById('drawer-edit-mode').style.display = 'flex';
    document.getElementById('drawer-add-member-area').style.display = 'block';
    document.getElementById('drawer-group-name').readOnly = false;
    document.getElementById('drawer-group-team').disabled = false;
    document.getElementById('member-search-input').value = '';
    renderMembers();
    renderUserSelectList(''); // ユーザー一覧を表示
    updateSelectedCount();
}

// 編集をキャンセル
function cancelDrawerEdit() {
    if (confirm('編集内容を破棄しますか？')) {
        isEditMode = false;
        selectedUserIds = [];
        document.getElementById('drawer-view-mode').style.display = 'flex';
        document.getElementById('drawer-edit-mode').style.display = 'none';
        document.getElementById('drawer-add-member-area').style.display = 'none';
        document.getElementById('drawer-group-name').readOnly = true;
            document.getElementById('drawer-group-team').disabled = true;
        document.getElementById('member-search-input').value = '';
        
        // データを再読み込み
        document.getElementById('drawer-group-name').value = currentGroupData.name;
        document.getElementById('drawer-group-team').value = currentGroupData.teamId;
        loadGroupMembers(currentGroupId);
    }
}

// ユーザー一覧を描画（フィルタ対応）
function renderUserSelectList(query) {
    const listEl = document.getElementById('user-select-list');
    const currentMemberIds = currentMembers.map(m => m.id);
    
    // 既存メンバーを除外してフィルタ
    let filtered = allUsers.filter(user => !currentMemberIds.includes(user.id));
    
    if (query && query.length > 0) {
        const lowerQuery = query.toLowerCase();
        filtered = filtered.filter(user => 
            user.displayName.toLowerCase().includes(lowerQuery) || 
            (user.kana && user.kana.toLowerCase().includes(lowerQuery)) ||
            (user.emailAddress && user.emailAddress.toLowerCase().includes(lowerQuery))
        );
    }
    
    if (filtered.length === 0) {
        listEl.innerHTML = '<div class="p-4 text-sm text-gray-500 text-center">追加可能なユーザーがいません</div>';
        return;
    }
    
    let html = '';
    filtered.forEach(user => {
        const isChecked = selectedUserIds.includes(user.id);
        html += `
            <label class="flex items-center gap-3 px-4 py-2 hover:bg-blue-50 cursor-pointer border-b last:border-b-0 transition-colors">
                <input type="checkbox" 
                       value="${user.id}" 
                       ${isChecked ? 'checked' : ''}
                       onchange="toggleUserSelection(${user.id})"
                       class="w-4 h-4 text-green-600 rounded focus:ring-green-500">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                    ${user.displayName.substring(0, 2)}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">${user.displayName}</div>
                    <div class="text-xs text-gray-500 truncate">${user.emailAddress || ''}</div>
                </div>
            </label>
        `;
    });
    
    listEl.innerHTML = html;
}

// 検索フィルタ
function filterUserList(query) {
    renderUserSelectList(query);
}

// ユーザー選択をトグル
function toggleUserSelection(userId) {
    const index = selectedUserIds.indexOf(userId);
    if (index === -1) {
        selectedUserIds.push(userId);
    } else {
        selectedUserIds.splice(index, 1);
    }
    updateSelectedCount();
}

// 選択件数を更新
function updateSelectedCount() {
    document.getElementById('selected-count').textContent = `${selectedUserIds.length}件選択`;
}

// 選択したユーザーを追加
function addSelectedUsers(isAdmin) {
    if (selectedUserIds.length === 0) {
        alert('ユーザーを選択してください');
        return;
    }
    
    selectedUserIds.forEach(userId => {
        const user = allUsers.find(u => u.id === userId);
        if (user && !currentMembers.find(m => m.id === userId)) {
            currentMembers.push({
                id: user.id,
                displayName: user.displayName,
                emailAddress: user.emailAddress || '',
                kana: user.kana || '',
                isAdmin: isAdmin
            });
        }
    });
    
    // 選択をリセット
    selectedUserIds = [];
    updateSelectedCount();
    document.getElementById('member-search-input').value = '';
    
    // リストを再描画
    renderUserSelectList('');
    renderMembers();
}

// メンバー権限を切り替え
function toggleMemberRole(userId) {
    const member = currentMembers.find(m => m.id === userId);
    if (member) {
        member.isAdmin = !member.isAdmin;
        renderMembers();
    }
}

// メンバーを削除
function removeMember(userId) {
    if (confirm('このメンバーをケースから削除しますか？')) {
        currentMembers = currentMembers.filter(m => m.id !== userId);
        renderMembers();
        // ユーザー選択リストも更新（削除したユーザーが再度選択可能になる）
        renderUserSelectList(document.getElementById('member-search-input').value);
    }
}

// 変更を保存
function saveGroupChanges() {
    const groupName = document.getElementById('drawer-group-name').value;
    const isPublic = 0; // 外来Lawでは強制非公開
    const teamId = document.getElementById('drawer-group-team').value;
    
    if (!groupName.trim()) {
        alert('ケース名を入力してください');
        return;
    }
    
    // 管理者と一般メンバーを分ける
    const adminIds = currentMembers.filter(m => m.isAdmin).map(m => m.id);
    const memberIds = currentMembers.map(m => m.id);  // 全メンバー
    
    // まず一般メンバーとして全員追加（editGroup）
    const groupsInfo = [{
        id: currentGroupId,
        name: groupName,
        public: isPublic,
        teamId: parseInt(teamId),
        avatarFileId: currentGroupData.avatarFileId,
        usersIds: memberIds
    }];
    
    // 次に管理者権限を付与（editGroupAdmin）
    const groupAdminsInfo = [{
        id: currentGroupId,
        name: groupName,
        public: isPublic,
        avatarFileId: currentGroupData.avatarFileId,
        usersIds: adminIds
    }];
    
    // フォームを作成して送信（両方のデータを含める）
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ action('MainController@editGroup') }}";
    form.style.display = 'none';
    
    // CSRFトークン
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // グループ情報（全メンバー）
    const dataInput = document.createElement('input');
    dataInput.type = 'hidden';
    dataInput.name = 'groupsInfo';
    dataInput.value = JSON.stringify(groupsInfo);
    form.appendChild(dataInput);
    
    // 管理者情報
    const adminInput = document.createElement('input');
    adminInput.type = 'hidden';
    adminInput.name = 'groupAdminsInfo';
    adminInput.value = JSON.stringify(groupAdminsInfo);
    form.appendChild(adminInput);
    
    document.body.appendChild(form);
    form.submit();
}

// グループ削除確認
function confirmDeleteGroup() {
    if (!confirm('このケースを削除しますか？\nこの操作は取り消せません。')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ action('MainController@deleteGroup') }}";
    form.style.display = 'none';
    
    // CSRFトークン
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // グループID
    const groupIdsInput = document.createElement('input');
    groupIdsInput.type = 'hidden';
    groupIdsInput.name = 'groupIds';
    groupIdsInput.value = currentGroupId;
    form.appendChild(groupIdsInput);
    
    document.body.appendChild(form);
    form.submit();
}

// 新規追加モーダルを開く
function openAddGroupModal() {
    if (typeof openGroupModal !== 'undefined') {
        openGroupModal('groupAdd');
    }
}

// Escキーでドロワーを閉じる
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const backdrop = document.getElementById('drawer-backdrop');
        if (backdrop && !backdrop.classList.contains('invisible')) {
            closeDrawer();
        }
    }
});
</script>