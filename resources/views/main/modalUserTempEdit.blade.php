<div class="modal fade" id="userTempEditModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@editUserTemp')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">仮ユーザ編集</h4>
                </div>
                <div id="editUserTempModalBody" class="modal-body">
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

function editUserTempList(e){

    var checkList = document.getElementsByClassName('delUserTempTarget');
    var userTempId = "";
    var userTempsInfo = [];

    // チームIDリスト取得
    for (let i = 0; i < checkList.length; i += 1) {
        if(checkList[i].checked) {
            userTempId = checkList[i].name;

            var userTempInfo = new Object();
            userTempInfo.id = userTempId;
            userTempInfo.name = document.getElementsByName("userTempName" + userTempId)[0].value;
            userTempInfo.kana = document.getElementsByName("userTempKana" + userTempId)[0].value;
            userTempInfo.password = document.getElementsByName("userTempPassword" + userTempId)[0].value;

            const select = document.getElementsByName("userTempTeamId" + userTempId)[0];
            userTempInfo.teamId = select.selectedIndex !== undefined ? select.options[select.selectedIndex].value : -1;

            userTempsInfo.push(userTempInfo);
        }
    }
    var data = JSON.stringify(userTempsInfo);

    let body = document.getElementById('editUserTempModalBody');
    body.insertAdjacentHTML('afterend', '<input type="hidden" name="userTempsInfo" value=\'' + data + '\'/>');
}

</script>
