<div class="modal fade" id="groupEditModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@editGroup')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">グループ編集</h4>
                </div>
                <div id="editGroupModalBody" class="modal-body">
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

function editGroupList(e){

    var checkList = document.getElementsByClassName('delGroupTarget');
    var groupId = "";
    var groupsInfo = [];
    
    // チームIDリスト取得
    for (let i = 0; i < checkList.length; i += 1) {
        if(checkList[i].checked) {
            groupId = checkList[i].name;

            var groupInfo = new Object();
            groupInfo.id = groupId;
            var indexGroupPublicSelect = document.getElementById("groupPublicSelect"+ groupId).selectedIndex;
            groupInfo.public = document.getElementsByName("groupPublic" + groupId)[indexGroupPublicSelect].value;
            groupInfo.name = document.getElementsByName("groupName" + groupId)[0].value;
            groupInfo.avatarFileId = document.getElementsByName("groupAvatarFileId" + groupId)[0].value;
            
            var checks = document.getElementsByName('groupMember' + groupId);
            groupInfo.usersIds = [];

            for ( j = 0; j < checks.length; j++) {
                if ( checks[j].checked === true ) {
                    groupInfo.usersIds.push(parseInt(checks[j].value));
                }
            }

            groupsInfo.push(groupInfo);
        }
    }
    var data = JSON.stringify(groupsInfo);

    let body = document.getElementById('editGroupModalBody');
    body.insertAdjacentHTML('afterend', '<input type="hidden" name="groupsInfo" value=\'' + data + '\'/>');
}

</script>
