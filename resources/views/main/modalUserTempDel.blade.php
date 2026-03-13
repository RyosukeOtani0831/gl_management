<div class="modal fade" id="userTempDelModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@deleteUserTemp')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">ユーザー削除</h4>
                </div>
                <div id="delUserTempModalBody" class="modal-body">
                    <p>選択したユーザーを削除しますか？</p>
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

function delUserTempList(e){
    var checkList = document.getElementsByClassName('delUserTempTarget');
    let body = document.getElementById('delUserTempModalBody');
    var userTempIds = "";
    var userTempId = "";

    for (let i = 0; i < checkList.length; i += 1) {
        if (checkList[i].checked === true) {
            userTempId="";

            if(userTempIds.length > 0){
                userTempId += ",";
            }
            userTempId += checkList[i].name;
            userTempIds += userTempId;
        }

    }
    body.insertAdjacentHTML('afterend', '<input type="hidden" name="userTempIds" value="' + userTempIds + '" checked/>');
}

</script>
