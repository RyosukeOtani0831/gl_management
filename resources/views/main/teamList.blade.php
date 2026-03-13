<div id="team" class="tab-pane list_body hidden">
    <!-- ヘッダーセクション -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">チームリスト</h2>
            <p id="teamCount" class="text-sm text-gray-600 mt-1">チーム数：{{ count($teamList) }}</p>
            <p id="checkTeamCount" class="text-sm text-blue-600 mt-1">&nbsp;</p>
        </div>
        
        <!-- 通常モードのボタン群 -->
        <div id="buttonsTeam" class="flex flex-wrap gap-2">
            <button type="button" onclick="window.location.href='./files/templateTeams.csv'" 
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition">
                CSVテンプレートをDL
            </button>
            <button type="button" onclick="openTeamModal('teamCsvAdd')" 
                    class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500 transition">
                インポート
            </button>
            <button type="button" onclick="reloadWithHash('team');" 
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
                <i class="fa fa-refresh mr-2" aria-hidden="true"></i> 更新
            </button>
            <button type="button" onclick="openTeamModal('teamAdd')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                追加
            </button>
            <button type="button" onclick="editModeTeamON()" 
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                編集
            </button>
            <button type="button" onclick="openTeamModal('teamDel')" 
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                削除
            </button>
        </div>
        
        <!-- 編集モード用のボタン群 -->
        <div id="exeEditTeam" class="flex gap-2" style="display: none;">
            <button type="button" onclick="openTeamModal('teamEdit')" 
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                確定
            </button>
            <button type="button" onclick="openTeamModal('teamEditCancel')" 
                    class="bg-gray-600 text-white px-6 py-2 rounded hover:bg-gray-700 transition">
                キャンセル
            </button>
        </div>
    </div>

    <!-- テーブルセクション -->
    <form id="teamList">
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <div style="max-height: calc(100vh - 280px); overflow-y: auto;">
                <table class="min-w-full text-sm text-left table-fixed">
                    <thead class="bg-gray-50 sticky-top" style="z-index: 10;">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="width: 50px;">#</th>
                            <th class="px-4 py-3 font-semibold text-gray-700" style="width: 50px;">
                                <i class="fas fa-check"></i>
                            </th>
                            <th class="px-4 py-3 font-semibold text-gray-700">Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamList as $i => $team)
                        <tr id="{{$team['id']}}" class="hover:bg-green-50 border-b">
                            <td class="px-4 py-2 text-gray-600">{{$i+1}}</td>
                            <td class="px-4 py-2">
                                <input type="checkbox" class="delTeamTarget w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                                       name="teamIds[]" value="{{$team['id']}}">
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="teamName{{$team['id']}}" value="{{$team['name']}}" 
                                       disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100" />
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- <div class="mt-4 text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                保存
            </button>
        </div> -->
    </form>

    <!-- 既存のモーダルファイルをインクルード -->
    <!-- @include('main/modalTeamCsv')
    @include('main/modalTeamAdd')
    @include('main/modalTeamDel')
    @include('main/modalTeamEdit')
    @include('main/modalReturnMainTeam') -->

    @include('main/modalTeam')


</div>

<script>
window.addEventListener('load', function() {
    editModeTeamOFF();
});

function editModeTeamON(){
    // ボタン群（追加、編集、削除など）を非表示にする
    const buttonsTeam = document.getElementById('buttonsTeam');
    if (buttonsTeam) {
        buttonsTeam.style.setProperty('display', 'none', 'important');
    }

    // 確定ボタンとキャンセルボタンを表示
    const exeEditTeam = document.getElementById('exeEditTeam');
    if (exeEditTeam) {
        exeEditTeam.style.display = "flex";
    }

    // フォームの入力を有効にする（チェックボックスを除く）
    const form = document.getElementById('teamList');
    [...form.elements].forEach(e => {
        if (!e.classList.contains("delTeamTarget")) {
            e.disabled = false;
            e.classList.remove('disabled:bg-gray-100');
            e.classList.add('bg-white');
        } else {
            e.disabled = true;
        }
    });
}

function editModeTeamOFF(){
    const form = document.getElementById('teamList');
    if (form) {
        [...form.elements].forEach(e => {
            if (!e.classList.contains("delTeamTarget")) {
                e.disabled = true;
                e.classList.add('disabled:bg-gray-100');
                e.classList.remove('bg-white');
            } else {
                e.disabled = false;
            }
        });
    }

    const buttonsTeam = document.getElementById('buttonsTeam');
    const exeEditTeam = document.getElementById('exeEditTeam');

    if (buttonsTeam) {
        buttonsTeam.style.display = "flex";
    }

    if (exeEditTeam) {
        exeEditTeam.style.display = "none";
    }
}

// チェックボックスのカウント更新
document.querySelectorAll('.delTeamTarget').forEach(function(element){
    element.addEventListener('change', function(click_element){
        const el = document.getElementsByClassName("delTeamTarget");
        let count = 0;

        for (let i = 0; i < el.length; i++) {
            if (el[i].checked) {
                count++;
            }
        }

        const count_text = document.getElementById('checkTeamCount');
        count_text.innerHTML = count > 0 ? "チェック数：" + count : "&nbsp;";
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // 全てのinput要素を取得
    var inputs = document.querySelectorAll("input[type='text'], input[type='date'], select");

    // 各input要素にイベントリスナーを追加
    inputs.forEach(function(input) {
        input.addEventListener("change", function() {
            // 対応するチェックボックスを取得
            var checkbox = this.closest("tr").querySelector("input[type='checkbox'].delTeamTarget");

            // チェックボックスをONにする
            if (checkbox) {
                checkbox.checked = true;
                // カウントを更新
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
});
</script>