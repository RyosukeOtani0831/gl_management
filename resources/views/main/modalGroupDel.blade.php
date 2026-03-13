<div class="modal fade" id="groupDelModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@deleteGroup')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">グループ削除</h4>
                </div>
                <div id="delGroupModalBody" class="modal-body">
                    <p>選択したグループを削除しますか？</p>
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

function delGroupList(e){
    var checkList = document.getElementsByClassName('delGroupTarget');
    let body = document.getElementById('delGroupModalBody');
    var groupIds = "";
    var groupId = "";

    for (let i = 0; i < checkList.length; i += 1) {
        if (checkList[i].checked === true) {
            groupId="";

            if(groupIds.length > 0){
                groupId += ",";
            }
            groupId += checkList[i].name;
            groupIds += groupId;
        }

    }
    body.insertAdjacentHTML('afterend', '<input type="hidden" name="groupIds" value="' + groupIds + '" checked/>');
}

</script>
