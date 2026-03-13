<div class="modal fade" id="teamEditModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@editTeam')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">チーム編集</h4>
                </div>
                <div id="editTeamModalBody" class="modal-body">
                    <p>変更を確定しますか？</p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-light" data-dismiss="modal">閉じる</a>
                    <button type="submit" class="btn btn-danger">確定</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

function editTeamList(e){

    var checkList = document.getElementsByClassName('delTeamTarget');
    var teamId = "";
    var teamsInfo = [];

    // チームIDリスト取得
    for (let i = 0; i < checkList.length; i += 1) {
        if(checkList[i].checked) {
            teamId = checkList[i].value;

            var teamInfo = new Object();
            teamInfo.id = teamId;
            teamInfo.name = document.getElementsByName("teamName" + teamId)[0].value;

            teamsInfo.push(teamInfo);
        }
    }
    var data = JSON.stringify(teamsInfo);

    let body = document.getElementById('editTeamModalBody');
    body.insertAdjacentHTML('afterend', '<input type="hidden" name="teamsInfo" value=\'' + data + '\'/>');
}

</script>
