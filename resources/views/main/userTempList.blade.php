<div id="userTemp" class="tab-pane list_body hidden">
    <!-- ヘッダーセクション -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">仮ユーザーリスト</h2>
            <p id="userTempCount" class="text-sm text-gray-600 mt-1">仮ユーザ数：{{ count($userTempList) }}</p>
            <p id="checkUserTempCount" class="text-sm text-blue-600 mt-1">&nbsp;</p>
        </div>
        
        <!-- 通常モードのボタン群 -->
        <div id="buttonsUserTemp" class="flex flex-wrap gap-2">
            <button type="button" onclick="reloadWithHash('userTemp');" 
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
                <i class="fa fa-refresh mr-2" aria-hidden="true"></i> 更新
            </button>
            <button type="button" onclick="openUserTempModal('userTempAdd')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                追加
            </button>
            <button type="button" onclick="editModeUserTempON()" 
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                編集
            </button>
            <button type="button" onclick="openUserTempModal('userTempDel')" 
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                削除
            </button>
        </div>
        
        <!-- 編集モード用のボタン群 -->
        <div id="exeEditUserTemp" class="flex gap-2" style="display: none;">
            <button type="button" onclick="openUserTempModal('userTempEdit')" 
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                確定
            </button>
            <button type="button" onclick="openUserTempModal('userTempEditCancel')" 
                    class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                キャンセル
            </button>
        </div>
    </div>

    <!-- テーブルセクション -->
    <form id="userTempList">
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <div style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-50 sticky top-0" style="z-index: 10;">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="width: 50px;">#</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="width: 50px;">
                                <i class="fas fa-check"></i>
                            </th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="width: 200px;">Name</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="width: 200px;">kana</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="width: 200px;">password</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="width: 150px;">Team</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userTempList as $i => $userTemp)
                        <tr class="hover:bg-green-50 border-b">
                            <td class="px-4 py-2 text-gray-600">{{$i+1}}</td>
                            <td class="px-4 py-2">
                                <input type="checkbox" class="delUserTempTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                                       value="{{$userTemp->id}}">
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="userTempName{{$userTemp->id}}" value="{{$userTemp->name}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="userTempKana{{$userTemp->id}}" value="{{$userTemp->kana}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="userTempPassword{{$userTemp->id}}" value="{{$userTemp->password}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                <select name="userTempTeamId{{$userTemp->id}}" disabled
                                        class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100">
                                    <option value=""></option>
                                    @foreach($teamList as $team)
                                        @if($userTemp->team && $userTemp->team == $team['id'])
                                            <option value="{{$team['id']}}" selected>{{$team['name']}}</option>
                                        @else
                                            <option value="{{$team['id']}}">{{$team['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    @include('main/modalUserTemp')

</div>

<script>
window.addEventListener('load', function() {
    editModeUserTempOFF();
});

function editModeUserTempON(){
    const buttonsUserTemp = document.getElementById('buttonsUserTemp');
    if (buttonsUserTemp) {
        buttonsUserTemp.style.setProperty('display', 'none', 'important');
    }

    const exeEditUserTemp = document.getElementById('exeEditUserTemp');
    if (exeEditUserTemp) {
        exeEditUserTemp.style.display = "flex";
    }

    const form = document.getElementById('userTempList');
    [...form.elements].forEach(e => {
        if (!e.classList.contains("delUserTempTarget")) {
            e.disabled = false;
            e.classList.remove('disabled:bg-gray-100');
            e.classList.add('bg-white');
        } else {
            e.disabled = true;
        }
    });
}

function editModeUserTempOFF(){
    const form = document.getElementById('userTempList');
    if (form) {
        [...form.elements].forEach(e => {
            if (!e.classList.contains("delUserTempTarget")) {
                e.disabled = true;
                e.classList.add('disabled:bg-gray-100');
                e.classList.remove('bg-white');
            } else {
                e.disabled = false;
            }
        });
    }

    const buttonsUserTemp = document.getElementById('buttonsUserTemp');
    const exeEditUserTemp = document.getElementById('exeEditUserTemp');

    if (buttonsUserTemp) {
        buttonsUserTemp.style.display = "flex";
    }

    if (exeEditUserTemp) {
        exeEditUserTemp.style.display = "none";
    }
}

document.querySelectorAll('.delUserTempTarget').forEach(function(element){
    element.addEventListener('change', function(){
        const el = document.getElementsByClassName("delUserTempTarget");
        let count = 0;

        for (let i = 0; i < el.length; i++) {
            if (el[i].checked) {
                count++;
            }
        }

        const count_text = document.getElementById('checkUserTempCount');
        count_text.innerHTML = count > 0 ? "チェック数：" + count : "&nbsp;";
    });
});

document.addEventListener("DOMContentLoaded", function() {
    var inputs = document.querySelectorAll("input[type='text'], select");

    inputs.forEach(function(input) {
        input.addEventListener("change", function() {
            var checkbox = this.closest("tr").querySelector("input[type='checkbox'].delUserTempTarget");
            if (checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
});
</script>