<div class="modal fade" id="teamCsvModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <form role="form" class="form-inline" method="post" action="{{ action('CsvController@importTeamCsv')}}" enctype="multipart/form-data">
    @csrf
        <div class="modal-dialog modal-dialog-centerd role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">CSVインポート</h4>
                </div>
                <div id="TeamCsvModalBody" class="modal-body">
                    <p>CSVファイルを選択してください</p>
                    <input type="file" name="teamCsvFile" class="" id="teamCsvFile"/>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-light" data-dismiss="modal">閉じる</a>
                    <button type="submit" class="btn btn-danger">インポート</button>
                </div>
            </div>
        </div>
    </form>
</div>