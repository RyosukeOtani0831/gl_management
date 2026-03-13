<div id="delivery" class="tab-pane h-100">
    <div class="row" style="height:10%">
        <div class="col">
            <nav class="navbar px-3">
                <div class="navbar-brand toggle-menu">
                    <h2>テキスト配信</h2>
                </div>
            </nav>
        </div>
    </div>

    <div class="row overflow-auto" style="height:90%;padding-bottom: 80px;">
        <div class="col rborder border-warning" style="padding-left: 0px;">
            <table class="table text-nowrap table-fixed">
                <thead class="sticky-top bg-white">
                <tr>
                        <th class="fixed01" scope="col"> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <form role="form" class="form-inline" method="post" action="{{ action('MainController@sendManagementMessage')}}" onkeydown="if(!['textarea','submit'].includes(event.target.type) && event.keyCode == 13){return false;}">
                            @csrf
                                <div class="modal-dialog modal-dialog-centerd" style="max-width:800px;">
                                    <div class="modal-content">
                                        <div class="modal-body">

                                        <div class="form-group row">
                                            <p class="col-sm-2 col-form-label">グループ</p>
                                            <div class="col-sm-10">
                                                <p class="modal-team"></p>
                                                <select class="modal-team" name="roomId" required>
                    
                                                @foreach($groupList as $group)
                                                    @php
                                                        $userGar = array();
                                                        foreach($group['users'] as $userG){
                                                            if($userG['isAdmin']){
                                                                array_push($userGar, $userG['userId']);
                                                            }
                                                        }
                                                    @endphp
                                                    @if($group['public'] && array_search($loginUserId, $userGar) !== false)
                                                        <option value="{{$group['id']}}">{{$group['name']}}</option>
                                                    @endif
                                                @endforeach
                                                </select>
                                                <p style="font-size:12px">※管理画面にログインしたユーザーが管理者になっている【公開グループ】にのみ投稿が可能です。</p>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                                <p class="col-sm-2 col-form-label">投稿内容</p>
                                                <div class="col-sm-10">
                                                    <textarea name="text" value="" required style="resize: none; width:600px;height:200px;" white-space: pre-wrap;></textarea>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger">投稿</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('keydown',e=>{
  const selection = getSelection();
  const t=e.target;
  if(t.closest('textarea')){
    const v=t.value;
    const s=t.selectionStart;
    if(e.keyCode == 13){
      e.preventDefault();
      t.value=v.substring(0,s)+"\n"+v.substring(s);
      t.selectionStart=s+1;
      t.selectionEnd=s+1;
    }
  }
});
</script>