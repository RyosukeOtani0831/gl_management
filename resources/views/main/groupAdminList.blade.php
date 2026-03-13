<!-- ヘッダーセクション -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-semibold text-gray-900">ケースリスト（管理者）</h2>
        <p id="groupAdminCount" class="text-sm text-gray-600 mt-1">ケース数：{{ count($groupList) }}</p>
        <p id="checkGroupAdminCount" class="text-sm text-blue-600 mt-1">&nbsp;</p>
    </div>
    
    <!-- 通常モードのボタン群 -->
    <div id="buttonsGroupAdmin" class="flex flex-wrap gap-2">
        <button type="button" onclick="reloadWithHash('groupAdmin');" 
                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
            <i class="fa fa-refresh mr-2" aria-hidden="true"></i> 更新
        </button>
        <button type="button" onclick="editModeGroupAdminON()" 
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            編集
        </button>
    </div>
    
    <!-- 編集モード用のボタン群 -->
    <div id="exeEditGroupAdmin" class="flex gap-2" style="display: none;">
        <button type="button" onclick="openGroupAdminModal('groupAdminEdit')" 
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
            確定
        </button>
        <button type="button" onclick="openGroupAdminModal('groupAdminEditCancel')" 
                class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
            キャンセル
        </button>
    </div>
</div>

<!-- テーブルセクション -->
<form id="groupAdminList">
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
                            <input type="checkbox" class="delGroupAdminTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                                    value="{{$group['id']}}">
                        </td>
                        <td class="px-4 py-2">
                            <input type="text" name="groupAdminName{{$group['id']}}" value="{{$group['name']}}" 
                                    disabled
                                    class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                        </td>
                        <td class="px-4 py-2">
                            <select id="groupAdminPublicSelect{{$group['id']}}" disabled
                                    class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                @if($group['public'])
                                    <option name="groupAdminPublic{{$group['id']}}" value="0">非公開</option>
                                    <option name="groupAdminPublic{{$group['id']}}" value="1" selected>公開</option>
                                @else
                                    <option name="groupAdminPublic{{$group['id']}}" value="0" selected>非公開</option>
                                    <option name="groupAdminPublic{{$group['id']}}" value="1">公開</option>
                                @endif
                            </select>
                        </td>

                        @php
                            $userGar = array();
                            foreach($group['users'] as $userG){
                                if($userG['isAdmin']){
                                    array_push($userGar, $userG['userId']);
                                }
                            }
                        @endphp

                        @foreach($userListAll as $user)
                            <td class="px-4 py-2 text-center">
                                @if(array_search($user['id'], $userGar) === false)
                                    <input name="groupAdminMember{{$group['id']}}" 
                                            value="{{$user['id']}}" 
                                            type="checkbox" 
                                            disabled
                                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"/>
                                @else
                                    <input name="groupAdminMember{{$group['id']}}" 
                                            value="{{$user['id']}}" 
                                            type="checkbox" 
                                            checked 
                                            disabled
                                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"/>
                                @endif
                            </td>
                        @endforeach
                        
                        <input name="groupAdminAvatarFileId{{$group['id']}}" value="{{$group['avatarFileId'] ?? ''}}" type="hidden" />
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>

@include('main/modalGroupAdmin')


<script>
window.addEventListener('load', function() {
    editModeGroupAdminOFF();
});

function editModeGroupAdminON(){
    const buttonsGroupAdmin = document.getElementById('buttonsGroupAdmin');
    if (buttonsGroupAdmin) {
        buttonsGroupAdmin.style.setProperty('display', 'none', 'important');
    }

    const exeEditGroupAdmin = document.getElementById('exeEditGroupAdmin');
    if (exeEditGroupAdmin) {
        exeEditGroupAdmin.style.display = "flex";
    }

    const form = document.getElementById('groupAdminList');
    [...form.elements].forEach(e => {
        if (!e.classList.contains("delGroupAdminTarget")) {
            e.disabled = false;
            e.classList.remove('disabled:bg-gray-100');
            e.classList.add('bg-white');
        } else {
            e.disabled = true;
        }
    });
}

function editModeGroupAdminOFF(){
    const form = document.getElementById('groupAdminList');
    if (form) {
        [...form.elements].forEach(e => {
            if (!e.classList.contains("delGroupAdminTarget")) {
                e.disabled = true;
                e.classList.add('disabled:bg-gray-100');
                e.classList.remove('bg-white');
            } else {
                e.disabled = false;
            }
        });
    }

    const buttonsGroupAdmin = document.getElementById('buttonsGroupAdmin');
    const exeEditGroupAdmin = document.getElementById('exeEditGroupAdmin');

    if (buttonsGroupAdmin) {
        buttonsGroupAdmin.style.display = "flex";
    }

    if (exeEditGroupAdmin) {
        exeEditGroupAdmin.style.display = "none";
    }
}

document.querySelectorAll('.delGroupAdminTarget').forEach(function(element){
    element.addEventListener('change', function(){
        const el = document.getElementsByClassName("delGroupAdminTarget");
        let count = 0;

        for (let i = 0; i < el.length; i++) {
            if (el[i].checked) {
                count++;
            }
        }

        const count_text = document.getElementById('checkGroupAdminCount');
        count_text.innerHTML = count > 0 ? "チェック数：" + count : "&nbsp;";
    });
});

document.addEventListener("DOMContentLoaded", function() {
    var inputs = document.querySelectorAll("input[type='text'], input[type='date'], input[type='checkbox']:not(.delGroupAdminTarget), select");

    inputs.forEach(function(input) {
        input.addEventListener("change", function() {
            var checkbox = this.closest("tr").querySelector("input[type='checkbox'].delGroupAdminTarget");
            if (checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
});
</script>