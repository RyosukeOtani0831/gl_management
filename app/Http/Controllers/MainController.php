<?php

namespace App\Http\Controllers;

use App\Http\Controllers\MedilineAPIController;
use App\Http\Controllers\MySQLController;
use App\Http\Controllers\CsvController;
use App\Jobs\DeleteUserJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    /**
     * グループ権限UIを非表示にするか（外来Lawでは常にtrue）
     * @return bool
     */
    public static function hideGroupPermission()
    {
        return true;
    }

    /**
     * 大分式仮登録フローを使うか（外来Lawでは常にfalse）
     * @return bool
     */
    public static function useProvisionalFlow()
    {
        return false;
    }

    public function setHash(Request $request)
    {
        session(['current_hash' => $request->input('hash', 'user')]);
        return response()->json(['success' => true]);
    }

    /**
     * メイン画面描画
     * @param string $page
     * @return \Illuminate\View\View
     */
    public static function drawMain($page = "")
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // ログイン確認: セッションに 'user' があるかチェック
        if (!session('user') || !session('workplaceId')) {
            return redirect()->route('login');
        }

        $data = self::updateDisplay();
        $data["page"] = $page;
        $data["hash"] = session('current_hash', 'user');
        session(['current_hash' => '']);

        return view('main/main', $data);
    }

    /**
     * ユーザーリストを50音順にソート
     * @param array $userList
     * @return array
     */
    public static function sortUserList($userList)
    {
        usort($userList, [self::class, 'sortByTeamAndKana']);
        
        foreach ($userList as &$user) {
            $user['validFrom'] = self::formatDate($user['validFrom']);
            $user['validTo'] = self::formatDate($user['validTo']);
        }

        return $userList;
    }

    /**
     * チームIDと50音順でソートするコールバック関数
     * @param array $a
     * @param array $b
     * @return int
     */
    private static function sortByTeamAndKana($a, $b)
    {
        if ($a['teamId'] !== $b['teamId']) {
            return $a['teamId'] - $b['teamId'];
        }
        return strcmp($a['kana'], $b['kana']);
    }

    /**
     * 日付が存在する場合にフォーマットを適用
     * @param string|null $date
     * @return string|null
     */
    private static function formatDate($date)
    {
        return $date ? Carbon::parse($date)->format('Y-m-d') : null;
    }

    /**
     * ユーザー情報に管理者コメントを追加
     * @param array $userList
     * @return array
     */
    public static function addUserInfoAuthComment($userList)
    {
        foreach ($userList as &$user) {
            $user['authDescription'] = self::getUserAuthComment($user['id']);
        }

        return $userList;
    }

    /**
     * ユーザーの管理者コメントを取得
     * @param int $userId
     * @return string
     */
    private static function getUserAuthComment($userId)
    {
        return DB::table('auth_comment')
            ->where('mediline_id', $userId)
            ->value('comment') ?? '';
    }

    /**
     * グループ情報にメッセージ数を追加
     * @param array $groupList
     * @return array
     */
    public static function addGroupInfoMessageCount($groupList)
    {
        foreach ($groupList as &$group) {
            $group['messageCount'] = self::getGroupMessageCount($group['id']);
        }

        return $groupList;
    }

    /**
     * グループのメッセージカウントを取得
     * @param int $groupId
     * @return int
     */
    private static function getGroupMessageCount($groupId)
    {
        try {
            $response = MedilineAPIController::getRoomMessageCount(['roomId' => $groupId]);

            if (isset($response['data']['messageCount'])) {
                return $response['data']['messageCount'];
            }

            \Log::warning('Message count not found for group ID ' . $groupId);
            return 0;
        } catch (\Exception $e) {
            \Log::warning('Failed to get message count for group ID ' . $groupId, [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }
    
    /**
     * 画面情報更新
     * @return array
     */
    public static function updateDisplay()
    {
        $data = [];

        $teamList = self::fetchApiData([MedilineAPIController::class, 'getTeamList'], 'list');
        $groupList = self::fetchApiData([MedilineAPIController::class, 'getGroupList'], 'list');
        
        $response1 = MedilineAPIController::getUserList(session('workplaceId'), 1, false);
        $response2 = MedilineAPIController::getUserList(session('workplaceId'), 2, false);
        $userList1 = $response1['data']['list'] ?? [];
        $userList2 = $response2['data']['list'] ?? [];
        $userList = array_merge($userList1, $userList2);
        $userList = self::addUserInfoAuthComment($userList);
        $userList = self::sortUserList($userList);
        
        $responseAll = MedilineAPIController::getUserList(session('workplaceId'), 1, true);
        $userListForGroupPage = $responseAll['data']['list'] ?? [];
        $userListForGroupPage = self::addUserInfoAuthComment($userListForGroupPage);
        $userListForGroupPage = self::sortUserList($userListForGroupPage);
        
        $userTempList = MySQLController::SelectUserAll();

        $groupPermissionInfo = ['hideGroupPermission' => self::hideGroupPermission()];

        $csvUserHeaders = array_merge(CsvController::CSV_USER_HEADER, CsvController::CSV_USER_EDIT_HEADER);

        $data = [
            'teamList' => $teamList,
            'groupList' => self::addGroupInfoMessageCount($groupList),
            'userList' => $userList,
            'userListAll' => $userListForGroupPage,
            'userTempList' => $userTempList,
            'groupPermissionInfo' => $groupPermissionInfo,
            'loginUserId' => session('userId'),
            'csvUserHeaders' => $csvUserHeaders,
        ];

        return $data;
    }

    /**
     * API呼び出し結果を取得
     * @param callable $apiCall
     * @param string $dataKey
     * @return array
     */
    private static function fetchApiData(callable $apiCall)
    {
        $response = $apiCall();
        if (isset($response['data']['list'])) {
            return $response['data']['list'];
        }

        \Log::error("Failed to fetch data");
        return [];
    }

    /**
     * チーム作成
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function createTeam(Request $request)
    {
        $data = ['name' => $request->name];
        MedilineAPIController::postCreateTeam($data);

        session(['current_hash' => 'team']);
        return redirect()->route('main');
    }

    /**
     * グループ作成
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function createGroup(Request $request)
    {
        $data = [
            'name' => $request->name,
            'public' => (int)$request->public,
        ];

        $res = MedilineAPIController::postCreateGroup($data);
        $groupId = $res['data']['group']['id'];

        MedilineAPIController::putGroupToTeam([
            'groupId' => $groupId,
            'teamId' => $request->teamId
        ]);

        session(['current_hash' => 'group']);
        return redirect()->route('main');
    }

    // ユーザー作成
    public static function createUser(Request $request)
    {
        $data = self::prepareUserData($request);

        $res = MedilineAPIController::postCreateUser($data);
        session(['current_hash' => 'user']);
        return redirect()->route('main');
    }

    // ユーザー削除
    public static function deleteUser(Request $request)
    {
        $userIds = explode(",", $request->userIds);

        foreach ($userIds as $userId) {
            DeleteUserJob::dispatch($userId, session('workplaceId'));
        }

        session(['current_hash' => 'user']);
        return redirect()->route('main');
    }

    // グループ削除
    public static function deleteGroup(Request $request)
    {
        $groupIds = explode(",", $request->groupIds);

        foreach ($groupIds as $groupId) {
            self::deleteGroupById($groupId);
        }

        session(['current_hash' => 'group']);
        return redirect()->route('main');
    }

    // チーム削除
    public static function deleteTeam(Request $request)
    {
        if ($request->has('teamIds') && !empty($request->input('teamIds'))) {
            $teamIds = explode(",", $request->input('teamIds'));
            foreach ($teamIds as $teamId) {
                self::deleteTeamById($teamId);
            }
            session(['current_hash' => 'team']);
            return redirect()->route('main');
        }

        return redirect()->route('main')->with('error', '削除するチームが選択されていません');
    }

    // ユーザー編集
    public static function editUser(Request $request)
    {
        $users = json_decode($request->usersInfo);

        foreach ($users as $user) {
            self::updateUser($user);
            self::updateAuthComment($user);
        }

        session(['current_hash' => 'user']);
        return redirect()->route('main');
    }

    // グループ編集
    public static function editGroup(Request $request)
    {
        \Log::info('editGroup request data:', [
            'groupAdminsInfo_raw' => $request->input('groupAdminsInfo'),
            'groupsInfo_raw' => $request->input('groupsInfo')
        ]);
        
        if ($request->has('groupAdminsInfo')) {
            $groupAdmins = json_decode($request->groupAdminsInfo);
            \Log::info('Decoded groupAdmins:', ['groupAdmins' => $groupAdmins]);
            self::editGroupMaster($groupAdmins, true);
        }
        
        if ($request->has('groupsInfo')) {
            $groups = json_decode($request->groupsInfo);
            \Log::info('Decoded groups (before filtering):', ['groups' => $groups]);
            
            $adminIds = [];
            if ($request->has('groupAdminsInfo')) {
                $groupAdmins = json_decode($request->groupAdminsInfo);
                foreach ($groupAdmins as $groupAdmin) {
                    if (isset($groupAdmin->usersIds)) {
                        $adminIds = array_merge($adminIds, $groupAdmin->usersIds);
                    }
                }
            }
            
            \Log::info('Admin IDs extracted:', ['adminIds' => $adminIds]);
            
            foreach ($groups as $group) {
                if (isset($group->usersIds)) {
                    $originalUsers = $group->usersIds;
                    $group->usersIds = array_values(array_diff($group->usersIds, $adminIds));
                    \Log::info('Filtered group users:', [
                        'groupId' => $group->id,
                        'original' => $originalUsers,
                        'filtered' => $group->usersIds
                    ]);
                }
            }
            
            if (!empty($groups[0]->usersIds)) {
                self::editGroupMaster($groups, false);
            }
        }

        session(['current_hash' => 'group']);
        return redirect()->route('main');
    }

    // グループadmin編集
    public static function editGroupAdmin(Request $request)
    {
        $groups = json_decode($request->groupAdminsInfo);
        self::editGroupMaster($groups, true);

        session(['current_hash' => 'groupAdmin']);
        return redirect()->route('main');
    }

    // チーム編集
    public static function editTeam(Request $request)
    {
        $teams = json_decode($request->teamsInfo);

        foreach ($teams as $team) {
            $data = [
                'id'=>$team->id,
                'name'=>$team->name,
            ];
    
            $res = MedilineAPIController::updateTeam($data);
        }

        session(['current_hash' => 'team']);
        return redirect()->route('main');
    }

    // ユーザーの日時から時間を削除して戻す
    private static function excludeTime($userList)
    {
        foreach ($userList as &$user) {
            $user['validFrom'] = self::formatDate($user['validFrom']);
            $user['validTo'] = self::formatDate($user['validTo']);
        }
    }

    // ユーザー一時保存
    public static function createUserTemp(Request $request)
    {
        $data = [
            'name' => $request->name,
            'kana' => $request->kana,
            'password' => $request->password,
            'team' => $request->team,
        ];

        MySQLController::InsertUser($data);
        session(['current_hash' => 'userTemp']);
        return redirect()->route('main');
    }

    // ユーザー一時編集
    public static function editUserTemp(Request $request)
    {
        $users = json_decode($request->userTempsInfo);

        foreach ($users as $user) {
            self::updateUserTemp($user);
        }

        session(['current_hash' => 'userTemp']);
        return redirect()->route('main');
    }

    // ユーザー一時削除
    public static function deleteUserTemp(Request $request)
    {
        $userIds = explode(",", $request->userTempIds);

        foreach ($userIds as $userId) {
            MySQLController::DeleteUser(['id' => $userId]);
        }

        session(['current_hash' => 'userTemp']);
        return redirect()->route('main');
    }

    // 管理者メッセージ送信
    public static function sendManagementMessage(Request $request)
    {
        $data = [
            'roomId' => $request->roomId,
            'type' => "text",
            'text' => $request->text,
            'userId' => session('userId'),
        ];

        MedilineAPIController::postManagementMessage($data);
        return redirect()->route('main');
    }

    // --- ヘルパー関数 ---
    
    private static function prepareUserData($request)
    {
        return [
            'displayName' => $request->name,
            'kana' => $request['kana'],
            'emailAddress' => $request->email,
            'teamId' => (int) $request['teamId'],
            'validFrom' => $request['validFrom'],
            'validTo' => $request['validTo'],
            'description' => $request->description,
            'accountType' => $request->accountType ?? 'internal',
        ];
    }

    private static function deleteGroupById($groupId)
    {
        $data = ['id' => $groupId];
        MedilineAPIController::deleteGroup($data);
    }

    private static function deleteTeamById($teamId)
    {
        $data = ['id' => $teamId];
        MedilineAPIController::deleteTeam($data);
    }

    private static function updateUser($user)
    {
        $data = [
            'id' => $user->id,
            'displayName' => $user->displayName,
            'kana' => $user->kana,
            'emailAddress' => $user->emailAddress,
            'teamId' => (int) $user->teamId,
            'validFrom' => $user->validFrom,
            'validTo' => $user->validTo,
            'description' => $user->description,
        ];
        MedilineAPIController::updateUser($data);
    }

    private static function updateAuthComment($user)
    {
        if (isset($user->authDescription)) {
            $authDescriptiontData = [
                'mediline_id' => $user->id,
                'comment' => $user->authDescription,
            ];

            DB::table('auth_comment')->updateOrInsert(
                ['mediline_id' => $user->id],
                $authDescriptiontData
            );
        }
    }

    private static function updateUserTemp($user)
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'kana' => $user->kana,
            'password' => $user->password,
            'team' => $user->teamId,
            'registred' => 0,
        ];

        MySQLController::UpdateUser($data);
    }

    /**
     * グループ情報の更新・削除処理（管理者権限に応じた処理分岐）
     */
    private static function editGroupMaster($groups, $admin)
    {
        $getApiGroupList = MedilineAPIController::getGroupList();
        $groupList = $getApiGroupList['data']['list'] ?? [];

        foreach ($groupList as $i => $group) {
            $allUserIds = [];
            $adminUserIds = [];
            
            foreach ($group['users'] as $groupUser) {
                $userId = $groupUser['userId'];
                $allUserIds[] = $userId;
                
                if ($groupUser['isAdmin']) {
                    $adminUserIds[] = $userId;
                }
            }
            
            $groupList[$i]['usersIds'] = array_values(array_unique($allUserIds));
            $groupList[$i]['adminUserIds'] = array_values(array_unique($adminUserIds));
        }

        foreach ($groups as $group) {
            $existingGroup = collect($groupList)->firstWhere('id', $group->id);

            if (!$existingGroup) {
                continue;
            }

            self::updateGroupIfChanged($group, $existingGroup);
            self::updateGroupMembersIfChanged($group, $existingGroup, $admin);
        }
    }

    private static function updateGroupIfChanged($group, $existingGroup)
    {
        $groupAvatarFileId = $group->avatarFileId ?? null;
        $existingAvatarFileId = $existingGroup['avatarFileId'] ?? null;
        
        $newTeamId = isset($group->teamId) ? (int)$group->teamId : null;
        
        $existingTeamId = null;
        if (isset($existingGroup['teams']) && !empty($existingGroup['teams'])) {
            $existingTeamId = (int)$existingGroup['teams'][0]['id'];
        }
        
        \Log::info('Group update debug:', [
            'group_id' => $group->id,
            'new_teamId' => $newTeamId,
            'existing_teamId' => $existingTeamId,
            'comparison' => $newTeamId != $existingTeamId ? 'DIFFERENT' : 'SAME'
        ]);

        if (
            ($existingGroup['public'] ?? null) != $group->public ||
            ($existingGroup['name'] ?? null) != $group->name ||
            $existingAvatarFileId != $groupAvatarFileId
        ) {
            $data = [
                'id' => $group->id,
                'public' => $group->public,
                'name' => $group->name,
            ];
            
            $avatarFileId = $groupAvatarFileId ?? $existingAvatarFileId;
            if ($avatarFileId !== null) {
                $data['avatarFileId'] = $avatarFileId;
            }
            
            MedilineAPIController::updateGroup($data);
        }

        if ($newTeamId !== null && $newTeamId !== $existingTeamId) {
            \Log::info('Updating group team:', [
                'groupId' => $group->id,
                'from_teamId' => $existingTeamId,
                'to_teamId' => $newTeamId
            ]);
            
            try {
                if ($existingTeamId !== null) {
                    MedilineAPIController::removeGroupFromTeam([
                        'groupId' => $group->id,
                        'teamId' => $existingTeamId
                    ]);
                }
                
                MedilineAPIController::putGroupToTeam([
                    'groupId' => $group->id,
                    'teamId' => $newTeamId
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to update group team:', [
                    'error' => $e->getMessage(),
                    'groupId' => $group->id,
                    'teamId' => $newTeamId
                ]);
            }
        }
    }

    private static function updateGroupMembersIfChanged($group, $existingGroup, $admin)
    {
        $newUsersIds = $group->usersIds ?? [];
        if (!is_array($newUsersIds)) {
            $newUsersIds = [];
        }
        
        $existingUsersIds = $existingGroup['usersIds'] ?? [];
        $existingAdminIds = $existingGroup['adminUserIds'] ?? [];
        
        \Log::info('updateGroupMembersIfChanged called:', [
            'groupId' => $group->id,
            'admin' => $admin,
            'newUsersIds' => $newUsersIds,
            'existingUsersIds' => $existingUsersIds,
            'existingAdminIds' => $existingAdminIds
        ]);
        
        if ($admin) {
            $newAdminsToAdd = array_diff($newUsersIds, $existingUsersIds);
            $membersToPromote = array_diff(
                array_intersect($newUsersIds, $existingUsersIds),
                $existingAdminIds
            );
            $adminsToDemote = array_diff($existingAdminIds, $newUsersIds);
            
            \Log::info('Admin processing:', [
                'newAdminsToAdd' => $newAdminsToAdd,
                'membersToPromote' => $membersToPromote,
                'adminsToDemote' => $adminsToDemote
            ]);
            
            if (!empty($newAdminsToAdd)) {
                \Log::info('Adding new admins:', ['userIds' => $newAdminsToAdd]);
                MedilineAPIController::updateGroupMember([
                    'id' => $group->id,
                    'usersIds' => array_values($newAdminsToAdd),
                    'admin' => true
                ]);
            }
            
            foreach ($membersToPromote as $userId) {
                \Log::info('Promoting member to admin:', ['userId' => $userId]);
                MedilineAPIController::deleteGroupMember([
                    'id' => $group->id,
                    'usersId' => $userId
                ]);
                MedilineAPIController::updateGroupMember([
                    'id' => $group->id,
                    'usersIds' => [$userId],
                    'admin' => true
                ]);
            }
            
            foreach ($adminsToDemote as $userId) {
                \Log::info('Demoting admin to member:', ['userId' => $userId]);
                MedilineAPIController::deleteGroupMember([
                    'id' => $group->id,
                    'usersId' => $userId
                ]);
            }
            
        } else {
            $newMembersToAdd = array_diff($newUsersIds, $existingUsersIds);
            $demotedAdmins = array_intersect($newUsersIds, $existingAdminIds);
            $demotedAdmins = array_diff($demotedAdmins, $existingUsersIds);
            $allToAdd = array_diff($newUsersIds, array_diff($existingUsersIds, $existingAdminIds));
            $existingMembers = array_diff($existingUsersIds, $existingAdminIds);
            $membersToRemove = array_diff($existingMembers, $newUsersIds);
            
            \Log::info('Member processing:', [
                'newMembersToAdd' => $newMembersToAdd,
                'membersToRemove' => $membersToRemove
            ]);
            
            if (!empty($newMembersToAdd)) {
                \Log::info('Adding new members:', ['userIds' => $newMembersToAdd]);
                MedilineAPIController::updateGroupMember([
                    'id' => $group->id,
                    'usersIds' => array_values($newMembersToAdd),
                    'admin' => false
                ]);
            }
            
            foreach ($membersToRemove as $userId) {
                \Log::info('Removing member:', ['userId' => $userId]);
                MedilineAPIController::deleteGroupMember([
                    'id' => $group->id,
                    'usersId' => $userId
                ]);
            }
        }
    }

    public function getUsersPaginated(Request $request)
    {
        $page = $request->query('page', 1);
        
        if ($page == 1) {
            $response1 = MedilineAPIController::getUserList(session('workplaceId'), 1);
            $response2 = MedilineAPIController::getUserList(session('workplaceId'), 2);
            
            $userList1 = $response1['data']['list'] ?? [];
            $userList2 = $response2['data']['list'] ?? [];
            $userList = array_merge($userList1, $userList2);
            
            $userList = self::addUserInfoAuthComment($userList);
            $userList = self::sortUserList($userList);
            
            return response()->json([
                'users' => $userList,
                'hasMore' => count($userList2) >= ($response2['data']['limit'] ?? 0),
                'nextPage' => 3,
                'total' => $response1['data']['count'] ?? 0
            ]);
        }
        
        $response = MedilineAPIController::getUserList(session('workplaceId'), $page);
        $userList = $response['data']['list'] ?? [];
        $userList = self::addUserInfoAuthComment($userList);
        $userList = self::sortUserList($userList);
        
        return response()->json([
            'users' => $userList,
            'hasMore' => count($userList) >= ($response['data']['limit'] ?? 0),
            'nextPage' => $page + 1,
            'total' => $response['data']['count'] ?? 0
        ]);
    }

    public function getGroupContent()
    {
        $data = $this->getGroupPageData();
        return view('main/groupList', $data)->render();
    }

    public function getGroupAdminContent()
    {
        $data = $this->getGroupPageData();
        return view('main/groupAdminList', $data)->render();
    }

    private function getGroupPageData()
    {
        try {
            $teamList = self::fetchApiData([MedilineAPIController::class, 'getTeamList']);
            $groupList = self::fetchApiData([MedilineAPIController::class, 'getGroupList']);
            $groupList = self::addGroupInfoMessageCount($groupList);
            
            try {
                $responseAll = MedilineAPIController::getUserList(session('workplaceId'), 1, true);
                $userListAll = $responseAll['data']['list'] ?? [];
                $userListAll = self::addUserInfoAuthComment($userListAll);
                $userListAll = self::sortUserList($userListAll);
            } catch (\Exception $e) {
                \Log::error('Failed to fetch all users for group page: ' . $e->getMessage());
                $userListAll = [];
            }
            
            $groupPermissionInfo = ['hideGroupPermission' => self::hideGroupPermission()];
            
            return [
                'teamList' => $teamList,
                'groupList' => $groupList,
                'userListAll' => $userListAll,
                'groupPermissionInfo' => $groupPermissionInfo,
                'loginUserId' => session('userId'),
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to get group page data: ' . $e->getMessage());
            
            return [
                'teamList' => [],
                'groupList' => [],
                'userListAll' => [],
                'groupPermissionInfo' => ['hideGroupPermission' => self::hideGroupPermission()],
                'loginUserId' => session('userId'),
            ];
        }
    }

    public function getGroupAdminMembers($groupId)
    {
        try {
            MedilineAPIController::auth();
            
            $groupId = (int)$groupId;
            $groupList = MedilineAPIController::getGroupList(session('workplaceId'))['data']['list'];
            $group = collect($groupList)->firstWhere('id', $groupId);
            
            if (!$group) {
                return response()->json(['error' => 'Group not found'], 404);
            }
            
            \Log::info('Group structure:', [
                'groupId' => $groupId,
                'group_users' => $group['users']
            ]);
            
            $memberData = [];
            foreach ($group['users'] as $groupUser) {
                \Log::info('Processing group user:', [
                    'userId' => $groupUser['userId'],
                    'isAdmin_raw' => $groupUser['isAdmin'],
                    'isAdmin_type' => gettype($groupUser['isAdmin'])
                ]);
                
                $memberData[$groupUser['userId']] = [
                    'isAdmin' => (bool)$groupUser['isAdmin']
                ];
            }
            
            \Log::info('MemberData:', $memberData);
            
            $responseAll = MedilineAPIController::getUserList(session('workplaceId'), 1, true);
            $userListAll = $responseAll['data']['list'] ?? [];
            
            $members = [];
            foreach ($userListAll as $user) {
                if (isset($memberData[$user['id']])) {
                    $isAdmin = $memberData[$user['id']]['isAdmin'];
                    
                    \Log::info('Adding member:', [
                        'userId' => $user['id'],
                        'displayName' => $user['displayName'],
                        'isAdmin' => $isAdmin,
                        'isAdmin_type' => gettype($isAdmin)
                    ]);
                    
                    $members[] = [
                        'id' => $user['id'],
                        'displayName' => $user['displayName'],
                        'emailAddress' => $user['emailAddress'] ?? '',
                        'kana' => $user['kana'] ?? '',
                        'isAdmin' => $isAdmin
                    ];
                }
            }
            
            \Log::info('Final members:', $members);
            
            return response()->json([
                'group' => [
                    'id' => $group['id'],
                    'name' => $group['name'],
                    'public' => $group['public'],
                    'avatarFileId' => $group['avatarFileId'] ?? '',
                    'teamId' => isset($group['teams'][0]) ? $group['teams'][0]['id'] : null
                ],
                'members' => $members,
                'allUsers' => $userListAll
            ]);
            
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}