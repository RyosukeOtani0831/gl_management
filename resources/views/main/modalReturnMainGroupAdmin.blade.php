<div class="modal fade" id="returnMainGroupAdminModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@drawMain')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">編集キャンセル</h4>
                </div>
                <div id="returnMainModalBody" class="modal-body">
                    <p>編集を破棄しますか？</p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-light" data-dismiss="modal">閉じる</a>
                    <button type="submit" class="btn btn-danger">破棄</button>
                </div>
            </div>
        </div>
    </form>
</div>
