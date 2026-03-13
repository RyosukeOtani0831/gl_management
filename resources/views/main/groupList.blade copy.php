<!-- ヘッダーセクション -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-gray-900">グループリスト</h2>
        <p id="groupCount" class="text-sm text-gray-600 mt-1">グループ数：{{ count($groupList) }}</p>
        <p id="checkGroupCount" class="text-sm text-blue-600 mt-1">&nbsp;</p>
    </div>
    
    <!-- 通常モードのボタン群 -->
    <div id="buttonsGroup" class="flex flex-wrap gap-2">
        <button type="button" onclick="window.location.href='./files/templateGroups.csv'" 
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
            CSVテンプレートをDL
        </button>
        <button type="button" onclick="openGroupModal('groupCsvAdd')" 
                class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500 transition">
            インポート
        </button>
        <button type="button" onclick="reloadWithHash('group');" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
            <i class="fa fa-refresh mr-2" aria-hidden="true"></i> 更新
        </button>
        <button type="button" onclick="openGroupModal('groupAdd')" 
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            追加
        </button>
        <button type="button" onclick="editModeGroupON()" 
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            編集
        </button>
        <button type="button" onclick="openGroupModal('groupDel')" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
            削除
        </button>
    </div>
    
    <!-- 編集モード用のボタン群 -->
    <div id="exeEditGroup" class="flex gap-2" style="display: none;">
        <button type="button" onclick="openGroupModal('groupEdit')" 
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
            確定
        </button>
        <button type="button" onclick="openGroupModal('groupEditCancel')" 
                class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
            キャンセル
        </button>
    </div>
</div>

<!-- テーブルセクション -->
<form id="groupList">
    <div class="bg-white rounded-lg shadow" style="overflow-x: auto;">
        <div style="max-height: calc(100vh - 280px); overflow-y: auto;">
            <table class="text-sm text-left" style="white-space: nowrap;">
                <thead class="bg-gray-50 sticky top-0" style="z-index: 10;">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 50px;">#</th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 50px;">
                            <i class="fas fa-check"></i>
                        </th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 250px;">Group Name</th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 150px;">Team</th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="min-width: 110px;">Public</th>
                        @foreach($userListAll as $user)
                            <th class="px-4 py-3 font-semibold text-gray-700">{{$user['displayName']}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach($groupList as $i => $group)
                    <tr class="hover:bg-green-50 border-b">
                        <td class="px-4 py-2 text-gray-600">{{$i+1}}</td>
                        <td class="px-4 py-2">
                            <input type="checkbox" class="delGroupTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                                    value="{{$group['id']}}">
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" name="groupName{{$group['id']}}" value="{{$group['name']}}" 
                                    disabled
                                    class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                        </td>
                        <td class="px-4 py-2">
                            <select disabled name="groupTeamId{{$group['id']}}"
                                    class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                @foreach($teamList as $team)
                                    @if(isset($group['teams'][0]) && $group['teams'][0]['id'] == $team['id'])
                                        <option value="{{$team['id']}}" selected>{{$team['name']}}</option>
                                    @else
                                        <option value="{{$team['id']}}">{{$team['name']}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <select disabled name="groupPublic{{$group['id']}}"
                                    class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                @if($group['public'])
                                    <option value="0">非公開</option>
                                    <option value="1" selected>公開</option>
                                @else
                                    <option value="0" selected>非公開</option>
                                    <option value="1">公開</option>
                                @endif
                            </select>
                        </td>

                        @php
                            $userGar = array();
                            foreach($group['users'] as $userG){
                                array_push($userGar, $userG['userId']);
                            }
                        @endphp

                        @foreach($userListAll as $user)
                            <td class="px-4 py-2 text-center">
                                @if(array_search($user['id'], $userGar) === false)
                                    <input class="groupMember w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                                            name="groupMember{{$group['id']}}" 
                                            value="{{$user['id']}}" 
                                            type="checkbox" disabled/>
                                @else
                                    <input class="groupMember w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                                            name="groupMember{{$group['id']}}" 
                                            value="{{$user['id']}}" 
                                            type="checkbox" checked disabled/>
                                @endif
                            </td>
                        @endforeach

                        <input name="groupAvatarFileId{{$group['id']}}" value="{{$group['avatarFileId'] ?? ''}}" type="hidden" />
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>

@include('main/modalGroup')


<script>
window.addEventListener('load', function() {
    editModeGroupOFF();
});

function editModeGroupON(){
    const buttonsGroup = document.getElementById('buttonsGroup');
    if (buttonsGroup) {
        buttonsGroup.style.setProperty('display', 'none', 'important');
    }

    const exeEditGroup = document.getElementById('exeEditGroup');
    if (exeEditGroup) {
        exeEditGroup.style.display = "flex";
    }

    const form = document.getElementById('groupList');
    [...form.elements].forEach(e => {
        if (!e.classList.contains("delGroupTarget")) {
            e.disabled = false;
            e.classList.remove('disabled:bg-gray-100');
            e.classList.add('bg-white');
        } else {
            e.disabled = true;
        }
    });
}

function editModeGroupOFF(){
    const form = document.getElementById('groupList');
    if (form) {
        [...form.elements].forEach(e => {
            if (!e.classList.contains("delGroupTarget")) {
                e.disabled = true;
                e.classList.add('disabled:bg-gray-100');
                e.classList.remove('bg-white');
            } else {
                e.disabled = false;
            }
        });
    }

    const buttonsGroup = document.getElementById('buttonsGroup');
    const exeEditGroup = document.getElementById('exeEditGroup');

    if (buttonsGroup) {
        buttonsGroup.style.display = "flex";
    }

    if (exeEditGroup) {
        exeEditGroup.style.display = "none";
    }
}

document.querySelectorAll('.delGroupTarget').forEach(function(element){
    element.addEventListener('change', function(click_element){
        const el = document.getElementsByClassName("delGroupTarget");
        let count = 0;

        for (let i = 0; i < el.length; i++) {
            if (el[i].checked) {
                count++;
            }
        }

        const count_text = document.getElementById('checkGroupCount');
        count_text.innerHTML = count > 0 ? "チェック数：" + count : "&nbsp;";
    });
});

document.addEventListener("DOMContentLoaded", function() {
    var inputs = document.querySelectorAll("input[type='text'], input[type='checkbox']:not(.delGroupTarget), select");

    inputs.forEach(function(input) {
        input.addEventListener("change", function() {
            var checkbox = this.closest("tr").querySelector("input[type='checkbox'].delGroupTarget");
            if (checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
});
</script>