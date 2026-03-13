<div class="modal fade" id="teamDelModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@deleteTeam')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">チーム削除</h4>
                </div>
                <div id="delTeamModalBody" class="modal-body">
                    <p>選択したチームを削除しますか？</p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-light" data-dismiss="modal">閉じる</a>
                    <button type="submit" class="btn btn-danger">削除</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

function delTeamList(e) {
    var checkList = document.getElementsByClassName('delTeamTarget');
    let body = document.getElementById('delTeamModalBody');
    var teamIds = "";

    for (let i = 0; i < checkList.length; i += 1) {
        if (checkList[i].checked === true) {
            if (teamIds.length > 0) {
                // 既に1つ以上IDがあればカンマを追加
                teamIds += ",";
            }
            teamIds += checkList[i].value; // チェックされた値を追加
        }
    }

    console.log(teamIds);

    // teamIdsが空でない場合にのみinput要素を追加
    if (teamIds.length > 0) {
        // 隠しフィールドにチームIDを追加
        body.insertAdjacentHTML('afterend', '<input type="hidden" name="teamIds" value="' + teamIds + '" />');
    }
}
    

</script>
