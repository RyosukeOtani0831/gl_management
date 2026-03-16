<?php
namespace App\Http\Controllers;

use App\Exceptions\RedirectExceptions;
use App\Jobs\DeleteUserJob;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CsvController extends Controller
{
    const CSV_TEAM_HEADER = ['name'];
    const CSV_GROUP_HEADER = ['name', 'team'];
    const CSV_USER_HEADER = [
        'displayName',
        'kana',
        'emailAddress',
        'teamName',
        'validFrom',
        'validTo',
        'description',
        'accountType',
    ];
    const CSV_USER_EDIT_HEADER = ['authDescription','medilineID','deleteFlag',];
    const CSV_USER_DEL_HEADER = ['delete', 'displayName', 'kana', 'emailAddress'];
    const CSV_USER_TEMP_HEADER = ['name', 'kana', 'password', 'team'];
    const CSV_USER_TEMP_OITA_HEADER = ['name', 'kana', 'password'];

    private function convertDateToTimestamp($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        // スラッシュ区切りの場合、ハイフン区切りに変換
        $dateString = str_replace('/', '-', $dateString);
        
        $timestamp = strtotime($dateString);
        
        if ($timestamp === false) {
            return null;
        }
        
        return $timestamp * 1000; // ミリ秒に変換
    }

    // ファイルアップロード・読み込み・チェックを共通化
    private function processCsvUpload(Request $request, $fileKey, $header, $storedFileName)
    {
        try {
            // ファイルを保存
            if (!$request->hasFile($fileKey)) {
                throw new \Exception('CSVファイルの取得に失敗しました');
            }

            if ($request->$fileKey->getClientOriginalExtension() !== 'csv') {
                throw new \Exception('拡張子がcsvではありません');
            }

            $request->$fileKey->storeAs('public/', $storedFileName);

            // ファイル内容取得
            $csv = Storage::disk('local')->get('public/' . $storedFileName);

            // 改行コードを統一して、行単位のコレクションを作成
            $data = collect(explode("\n", str_replace(["\r\n", "\r"], "\n", $csv)));

            // ヘッダー項目数チェック
            $fileHeader = collect(explode(',', $data->shift()));
            if ($header->count() !== $fileHeader->count()) {
                throw new \Exception('CSVの項目数が一致しません');
            }

            return $data->map(function ($oneline) use ($header) {
                return strlen($oneline) > 0 ? $header->combine(collect(explode(',', $oneline))) : null;
            })->filter();
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    //
    // import user csv
    //
    public function importUserCsv(Request $request)
    {
        $users = $this->processCsvUpload($request, 'userCsvFile', collect(self::CSV_USER_HEADER), 'users.csv');

        $users->each(function ($user) {
            $data = [
                'displayName' => $user['displayName'],
                'kana' => $user['kana'],
                'emailAddress' => $user['emailAddress'],
                'teamId' => self::getTeamIdFromName($user['teamName']),
                'validFrom' => $this->convertDateToTimestamp($user['validFrom'] ?? null),
                'validTo' => $this->convertDateToTimestamp($user['validTo'] ?? null),
                'description' => $user['description'],
                'accountType' => $user['accountType'] ?? 'internal',
            ];
            MedilineAPIController::postCreateUser($data);
        });

        session(['current_hash' => 'user']);
        return redirect()->route('main');
    }

    //
    // import user edit csv
    //
    public function importUserEditCsv(Request $request)
    {
        // ヘッダーを結合
        $header = collect(array_merge(self::CSV_USER_HEADER, self::CSV_USER_EDIT_HEADER));
        
        // CSVファイルのアップロードと処理
        $users = $this->processCsvUpload($request, 'userCsvEditFile', $header, 'usersEdit.csv');
        
        // Mediline ID で既存ユーザーを検索し、データを更新
        $users->each(function ($user) {
            // Mediline ID でユーザー情報を取得
            $existingUser = MedilineAPIController::getUserDetail($user['medilineID'])['data']['user'];
    
            if (isset($existingUser['isBot']) && $existingUser['isBot'] === true) {
                return; // BOTはスキップ
            }

            if ($existingUser && $existingUser['workplaceId'] == session('workplaceId')) {
                // 既存のユーザー情報
                $existingData = [
                    'id' => $existingUser['id'],
                    'displayName' => $existingUser['displayName'] ?? '',
                    'kana' => $existingUser['kana'] ?? '',
                    'emailAddress' => $existingUser['emailAddress'] ?? '',
                    'teamId' => $existingUser['team']['id'] ?? null,
                    'validFrom' => $existingUser['validFrom'] ?? null,
                    'validTo' => $existingUser['validTo'] ?? null,
                    'description' => $existingUser['description'] ?? '',
                    // 'authDescription' => $existingUser['authDescription'],
                ];
        
                // 新しいデータ
                $newData = [
                    'id' => $user['medilineID'],
                    'displayName' => $user['displayName'],
                    'kana' => $user['kana'],
                    'emailAddress' => $user['emailAddress'],
                    'teamId' => self::getTeamIdFromName($user['teamName']),
                    'validFrom' => $this->convertDateToTimestamp($user['validFrom'] ?? null),
                    'validTo' => $this->convertDateToTimestamp($user['validTo'] ?? null),
                    'description' => $user['description'],
                    'accountType' => $user['accountType'] ?? 'internal',
                    // 'authDescription' => $user['authDescription'],
                ];
    
                // 変更があるか確認して更新
                // 既存データと新しいデータを比較
                foreach ($existingData as $key => $existingValue) {
                    // null と 空文字を同じとみなして比較
                    $existingValue = ($existingValue === null || $existingValue === '') ? '' : $existingValue;
                    $newValue = isset($newData[$key]) ? $newData[$key] : '';
                    
                    // null と 空文字を同じとみなして比較
                    $newValue = ($newValue === null || $newValue === '') ? '' : $newValue;

                    // 変更があれば更新
                    if ($existingValue !== $newValue) {
                        try {
                            // 更新処理
                            // var_dump($newData['displayName']);
                            // var_dump($existingValue);
                            // var_dump($newValue);
                            $response = MedilineAPIController::updateUser($newData);  // 実際にユーザーを更新します
                            
                            // 更新が失敗した場合、エラーレポート
                            if ($response['status'] !== 'success') {
                                report(new \Exception("ユーザー更新失敗: {$user['medilineID']}"));
                            }
                        } catch (\Exception $e) {
                            report($e);
                        }
                        break;  // 変更があれば1回だけ更新処理を実行する
                    }
                }
            } else {
                // ユーザーが見つからない、またはworkplaceIdが一致しない場合
                report(new \Exception("ユーザーが見つかりません: {$user['medilineID']}"));
            }
        });
        
        session(['current_hash' => 'user']);
        return redirect()->route('main')->with('success', 'ユーザー情報の一括編集が完了しました');
    }

    //
    // import user delete csv
    //
    public function importUserDelCsv(Request $request)
    {
        // CSVファイルを処理してユーザー情報を取得
        $header = collect(array_merge(self::CSV_USER_HEADER, self::CSV_USER_EDIT_HEADER));  // CSVユーザー編集ヘッダーを結合
        $users = $this->processCsvUpload($request, 'userCsvDelFile', $header, 'usersDel.csv');
    
        // 各ユーザーに対して削除ジョブを非同期で処理
        $users->each(function ($user) {
            if (isset($user['deleteFlag']) && $user['deleteFlag'] == 1) {
                // 非同期で削除処理を実行
                DeleteUserJob::dispatch($user['medilineID'], session('workplaceId'));
            }
        });
    
        // 完了メッセージ
        session(['current_hash' => 'user']);
        return redirect()->route('main')->with('success', 'ユーザー情報の一括削除がバックグラウンドで実行されています。');
    }
    
    //
    // import group csv
    //
    public function importGroupCsv(Request $request)
    {
        $groups = $this->processCsvUpload($request, 'groupCsvFile', collect(self::CSV_GROUP_HEADER), 'groups.csv');

        $groups->each(function ($group) {
            $groupList = collect(MedilineAPIController::getGroupList()['data']['list']);
            $foundGroup = $groupList->firstWhere('name', $group['name']);

            if (!$foundGroup) {
                $res = MedilineAPIController::postCreateGroup([
                    'name' => $group['name'],
                    'public' => 0, // 外来Lawでは強制非公開
                ]);

                $teamId = self::getTeamIdFromName($group['team']);
                MedilineAPIController::putGroupToTeam([
                    'groupId' => $res['data']['group']['id'],
                    'teamId' => $teamId,
                ]);
            }
        });

        session(['current_hash' => 'group']);
        return redirect()->route('main');
    }

    //
    // import team csv
    //
    public function importTeamCsv(Request $request)
    {
        $teams = $this->processCsvUpload($request, 'teamCsvFile', collect(self::CSV_TEAM_HEADER), 'teams.csv');

        $teams->each(function ($team) {
            MedilineAPIController::postCreateTeam(['name' => $team['name']]);
        });

        session(['current_hash' => 'team']);
        return redirect()->route('main');
    }

    // チーム名からチームIDを取得する関数
    public function getTeamIdFromName($teamName)
    {
        static $teamList = null;

        if ($teamList === null) {
            $teamList = MedilineAPIController::getTeamList()['data']['list'];
        }

        $team = collect($teamList)->firstWhere('name', $teamName);

        return $team ? $team['id'] : '';
    }
}