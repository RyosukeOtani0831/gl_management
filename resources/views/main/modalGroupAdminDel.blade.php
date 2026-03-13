<div class="modal fade" id="groupAdminDelModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@deleteGroupAdmin')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">ケース削除</h4>
                </div>
                <div id="delGroupAdminModalBody" class="modal-body">
                    <p>選択したケースを削除しますか？</p>
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

function delGroupAdminList(e){
    var checkList = document.getElementsByClassName('delGroupAdminAdminTarget');
    let body = document.getElementById('delGroupAdminModalBody');
    var groupAdminIds = "";
    var groupAdminId = "";

    for (let i = 0; i < checkList.length; i += 1) {
        if (checkList[i].checked === true) {
            groupAdminId="";

            if(groupAdminIds.length > 0){
                groupAdminId += ",";
            }
            groupAdminId += checkList[i].name;
            groupAdminIds += groupAdminId;
        }

    }
    body.insertAdjacentHTML('afterend', '<input type="hidden" name="groupAdminIds" value="' + groupAdminIds + '" checked/>');
}

</script>
