<div id="usageStatus" class="tab-pane list_body hidden">
    <!-- ヘッダーセクション -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900">利用状況</h2>
            <p id="groupCount" class="text-sm text-gray-600 mt-1">ケース数：{{ count($groupList) }}</p>
        </div>
        
        <div class="flex gap-2">
            <button type="button" onclick="reloadWithHash('usageStatus');" 
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
                <i class="fa fa-refresh mr-2" aria-hidden="true"></i> 更新
            </button>
        </div>
    </div>

    <!-- テーブルセクション -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <div style="max-height: calc(100vh - 280px); overflow-y: auto;">
            <table class="min-w-full text-sm text-left table-fixed">
                <thead class="bg-gray-50 sticky-top" style="z-index: 10;">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="width: 50px;">#</th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="width: 200px;">Case Name</th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="width: 150px;">Team</th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="width: 100px;">ステータス</th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="width: 100px;">ユーザ数</th>
                        <th class="px-4 py-3 font-semibold text-gray-700" style="width: 100px;">投稿数</th>
                    </tr>
                </thead>
                <tbody>
                    <form id="usageStatusList">
                        @foreach($groupList as $i => $group)
                        <tr class="hover:bg-green-50 border-b">
                            <td class="px-4 py-2 text-gray-600">{{$i+1}}</td>
                            <td class="px-4 py-2">
                                <input type="text" name="usageStatusName{{$group['id']}}" 
                                       value="{{$group['name']}}" disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" class="usageStatusTeam bg-gray-50 border border-gray-300 rounded-lg p-2 w-full disabled:bg-gray-100" 
                                       value="{{$group['teams'][0]['name'] ?? ''}}" disabled />
                            </td>
                            <td class="px-4 py-2">
                                @if(!empty($group['isClosed']))
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                        <i class="fa fa-check mr-1"></i>クローズ
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <input name="usageStatusMember{{$group['id']}}" 
                                       value="{{ count($group['users']) }}" type="text" disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full disabled:bg-gray-100" />
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="usageStatusMessageCount{{$group['id']}}" 
                                       value="{{$group['messageCount']}}" disabled
                                       class="bg-gray-50 border border-gray-300 rounded-lg p-2 w-full disabled:bg-gray-100" />
                            </td>
                        </tr>
                        @endforeach
                    </form>
                </tbody>
            </table>
        </div>
    </div>
</div>