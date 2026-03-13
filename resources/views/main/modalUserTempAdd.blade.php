<div class="modal fade" id="userTempAddModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('MainController@createUserTemp')}}">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">ユーザー作成</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group row">
                        <p class="col-sm-4 col-form-label">氏名</p>
                        <div class="col-sm-8">
                            <p class="modal-name"></p>
                            <input class="modal-name" type="text" name="name" value="" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <p class="col-sm-4 col-form-label">氏名（フリガナ）</p>
                        <div class="col-sm-8">
                            <p class="modal-kana"></p>
                            <input class="modal-kana" type="text" name="kana" value="" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <p class="col-sm-4 col-form-label">パスワード</p>
                        <div class="col-sm-8">
                            <p class="modal-password"></p>
                            <input class="modal-password" type="text" name="password" value="" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <p class="col-sm-4 col-form-label">Team</p>
                        <div class="col-sm-8">
                            <p class="modal-team"></p>
                            <select class="modal-team" name="team" required>

                            @foreach($teamList as $team)
                                <option value="{{$team['id']}}">{{$team['name']}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <a class="btn btn-light" data-dismiss="modal">閉じる</a>
                    <button type="submit" class="btn btn-danger">作成</button>
                </div>
            </div>
        </div>
    </form>
</div>
