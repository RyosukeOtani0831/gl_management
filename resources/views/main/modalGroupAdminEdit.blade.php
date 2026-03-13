<div class="modal fade" id="groupAdminEditModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@editGroupAdmin')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">ケース（管理者）編集</h4>
                </div>
                <div id="editGroupAdminModalBody" class="modal-body">
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

function editGroupAdminList(e){

    var checkList = document.getElementsByClassName('delGroupAdminTarget');
    var GroupAdminId = "";
    var groupAdminsInfo = [];

    // チームIDリスト取得
    for (let i = 0; i < checkList.length; i += 1) {
        if(checkList[i].checked) {
            groupAdminId = checkList[i].name;

            var groupAdminInfo = new Object();
            groupAdminInfo.id = groupAdminId;
            var indexGroupAdminPublicSelect = document.getElementById("groupAdminPublicSelect"+ groupAdminId).selectedIndex;
            groupAdminInfo.public = document.getElementsByName("groupAdminPublic" + groupAdminId)[indexGroupAdminPublicSelect].value;
            groupAdminInfo.name = document.getElementsByName("groupAdminName" + groupAdminId)[0].value;
            groupAdminInfo.avatarFileId = document.getElementsByName("groupAdminAvatarFileId" + groupAdminId)[0].value;
            
            var checks = document.getElementsByName('groupAdminMember' + groupAdminId);
            groupAdminInfo.usersIds = [];

            for ( j = 0; j < checks.length; j++) {
                if ( checks[j].checked === true ) {
                    groupAdminInfo.usersIds.push(parseInt(checks[j].value));
                }
                console.log(groupAdminInfo.usersIds);
            }

            groupAdminsInfo.push(groupAdminInfo);
        }
    }
    var data = JSON.stringify(groupAdminsInfo);

    let body = document.getElementById('editGroupAdminModalBody');
    body.insertAdjacentHTML('afterend', '<input type="hidden" name="groupAdminsInfo" value=\'' + data + '\'/>');
}

</script>
