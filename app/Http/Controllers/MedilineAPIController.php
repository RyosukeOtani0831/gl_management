<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Exceptions\RedirectExceptions;

class MedilineAPIController extends Controller
{
    private static function baseUrl(): string
   {
        return rtrim(env('GAIRAILAW_API_URL'), '/');
    }
 
    private static function adminUsername(): string
    {
        return env('GAIRAILAW_ADMIN_USERNAME');
    }
 
    private static function adminPassword(): string
    {
        return env('GAIRAILAW_ADMIN_PASSWORD');
    }

    /**
     * セッション内の adminToken を確認し、なければ認証を行う
     */
    private static function sessionCheck()
    {
        $token = session('adminToken');
        $expiresAt = session('adminTokenExpiresAt');

        // トークンが無い、または期限切れなら再取得
        if (!$token || !$expiresAt || now()->gte($expiresAt)) {
            $tokenData = self::auth();
            session([
                'adminToken' => $tokenData['token'],
                'adminTokenExpiresAt' => now()->addSeconds($tokenData['expires_in'] ?? 3600),
            ]);
        }
    }

    /**
     * 管理者認証
     * @return string
     */
    public static function auth()
    {
        try {
            $client = new Client();

            $response = $client->post(self::baseUrl() . '/api/management/auth', [
                'json' => [
                    'username' => self::adminUsername(),
                    'password' => self::adminPassword(),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return [
                'token' => $data['data']['token'] ?? '',
                'expires_in' => $data['data']['expires_in'] ?? 3600,
            ];

        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * workplace一覧を取得
     * @return array
     */
    public static function getWorkplaceList()
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $response = $client->get(self::baseUrl() . '/api/management/workplaces', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * ユーザー一覧を取得
     * @param int $workplaceId
     * @param int $page
     * @param bool $fetchAll 全件取得フラグ（ログイン時などに使用）
     * @return array
     */
    public static function getUserList($workplaceId = -1, $page = 1, $fetchAll = false, $email = null)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        if (session()->has('workplaceId')) {
            $workplaceId = session('workplaceId');
        }

        try {
            $client = new Client();

            $query = [
                'page' => $page,
                'workplaceId' => $workplaceId,
            ];

            if ($email) {
                $query['email'] = $email;
            }

            $response = $client->get(self::baseUrl() . '/api/management/users', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
                'query' => $query,
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            if ($fetchAll && !$email) {
                $remaining = $data['data']['count'] - $data['data']['limit'] * $page;
                if ($remaining > 0) {
                    try {
                        $nextPageData = self::getUserList($workplaceId, $page + 1, true, null);
                        $data['data']['list'] = array_merge($data['data']['list'], $nextPageData['data']['list']);
                    } catch (\Exception $e) {
                        \Log::warning("Failed to fetch page " . ($page + 1) . ": " . $e->getMessage());
                        \Log::warning("Returning " . count($data['data']['list']) . " users fetched so far");
                    }
                }
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * グループ一覧を取得
     */
    public static function getGroupList($workplaceId = -1, $page = 1)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        if (session()->has('workplaceId')) {
            $workplaceId = session('workplaceId');
        }

        try {
            $client = new Client();
            $response = $client->get(self::baseUrl() . '/api/management/groups', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
                'query' => [
                    'page' => $page,
                    'workplaceId' => $workplaceId,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            $remaining = (int)$data['data']['count'] - (int)$data['data']['limit'] * $page;
            if ($remaining > 0) {
                $nextPageData = self::getGroupList($workplaceId, $page + 1);
                $data['data']['list'] = array_merge($data['data']['list'], $nextPageData['data']['list']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * チーム一覧を取得
     */
    public static function getTeamList($page = 1)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $workplaceId = session('workplaceId');

        try {
            $client = new Client();
            $response = $client->get(self::baseUrl() . '/api/management/teams', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
                'query' => [
                    'page' => $page,
                    'workplaceId' => $workplaceId,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            $remaining = (int)$data['data']['count'] - (int)$data['data']['limit'] * $page;
            if ($remaining > 0) {
                $nextPageData = self::getTeamList($page + 1);
                $data['data']['list'] = array_merge($data['data']['list'], $nextPageData['data']['list']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * ユーザー詳細を取得
     */
    public static function getUserDetail($userId, $workplaceId = null)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        if ($workplaceId === null && session()->has('workplaceId')) {
            $workplaceId = session('workplaceId');
        }

        try {
            $client = new Client();
            $response = $client->get(self::baseUrl() . "/api/management/users/{$userId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
                'query' => [
                    'workplaceId' => $workplaceId,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * ユーザーを作成
     */
    public static function postCreateUser($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $response = $client->post(self::baseUrl() . '/api/management/users', [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
                'http_errors' => false,
            ]);

            $res = json_decode($response->getBody(), true);

            if ($res['status'] === 'error') {
                if ($res['message'] === 'Email is already in use') {
                    $res['message'] = 'このメールアドレスは既に登録されています。';
                }
                throw new \Exception($res['message']);
            }

            return $res;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * ユーザーを削除
     */
    public static function deleteUser($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $userId = $data['id'];
            $response = $client->delete(self::baseUrl() . "/api/management/users/{$userId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            \Log::emergency('deleteUser 呼ばれた', $data);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * ユーザーを更新
     */
    public static function updateUser($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $userId = $data['id'];

        try {
            $client = new Client();
            $response = $client->put(self::baseUrl() . "/api/management/users/{$userId}", [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * グループ詳細を取得
     */
    public static function getGroupDetail($groupId)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $response = $client->get(self::baseUrl() . "/api/management/groups/{$groupId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * グループ作成
     */
    public static function postCreateGroup($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $response = $client->post(self::baseUrl() . '/api/management/groups', [
                'json' => [
                    'name' => $data['name'],
                    'public' => $data['public'],
                    'avatarFileId' => '',
                    'workplaceId' => session('workplaceId'),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * グループ削除
     */
    public static function deleteGroup($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $groupId = $data['id'];

        try {
            $client = new Client();
            $response = $client->delete(self::baseUrl() . "/api/management/groups/{$groupId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * グループ更新
     */
    public static function updateGroup($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $groupId = $data['id'];

        try {
            $client = new Client();
            $response = $client->put(self::baseUrl() . "/api/management/groups/{$groupId}", [
                'json' => [
                    'avatarFileId' => (int)$data['avatarFileId'],
                    'public' => filter_var($data['public'], FILTER_VALIDATE_BOOLEAN),
                    'name' => $data['name'],
                    'workplaceId' => session('workplaceId'),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * グループメンバー追加
     */
    public static function updateGroupMember($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $groupId = $data['id'];

        try {
            $client = new Client();
            $response = $client->put(self::baseUrl() . "/api/management/groups/{$groupId}/add", [
                'json' => [
                    'usersIds' => $data['usersIds'],
                    'admin' => filter_var($data['admin'], FILTER_VALIDATE_BOOLEAN),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * グループメンバー削除
     */
    public static function deleteGroupMember($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $groupId = $data['id'];
        $userId = $data['usersId'];

        try {
            $client = new Client();
            $response = $client->delete(self::baseUrl() . "/api/management/groups/{$groupId}/{$userId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * チームにグループを追加
     */
    public static function putGroupToTeam($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $groupId = $data['groupId'];

        try {
            $client = new Client();
            $response = $client->put(self::baseUrl() . "/api/management/groups/{$groupId}/teams/add", [
                'json' => [
                    'teamsIds' => [(int)$data['teamId']],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * チームからグループが抜ける
     */
    public static function removeGroupFromTeam($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $roomId = $data['groupId'];
        $teamId = $data['teamId'];

        try {
            $client = new Client();
            $response = $client->delete(self::baseUrl() . "/api/management/groups/{$roomId}/teams/{$teamId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
                'json' => []
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * チーム作成
     */
    public static function postCreateTeam($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $response = $client->post(self::baseUrl() . '/api/management/teams', [
                'json' => [
                    'name' => $data['name'],
                    'workplaceId' => session('workplaceId'),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * チーム更新
     */
    public static function updateTeam($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $teamId = $data['id'];

        try {
            $client = new Client();
            $response = $client->put(self::baseUrl() . "/api/management/teams/{$teamId}", [
                'json' => [
                    'name' => $data['name'],
                    'workplaceId' => session('workplaceId'),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * チーム削除
     */
    public static function deleteTeam($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');
        $teamId = $data['id'];

        try {
            $client = new Client();
            $response = $client->delete(self::baseUrl() . "/api/management/teams/{$teamId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * 管理メッセージ送信
     */
    public static function postManagementMessage($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $response = $client->post(self::baseUrl() . '/api/management/message', [
                'json' => [
                    'roomId' => (int)$data['roomId'],
                    'type' => $data['type'],
                    'body' => [
                        'text' => $data['text'],
                        'fileId' => $data['fileId'] ?? null,
                        'thumbId' => $data['thumbId'] ?? null,
                    ],
                    'userId' => $data['userId'],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    public static function getRoomMessageCount($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        if (empty($adminToken)) {
            throw new \Exception('Admin token is missing.');
        }

        try {
            $client = new Client();
            $response = $client->get(self::baseUrl() . "/api/management/counts/messages/{$data['roomId']}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            report($e);
            throw new \Exception("Request failed: " . $e->getMessage());
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    public static function postUploadFile($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $response = $client->post(self::baseUrl() . '/api/upload/files', [
                'json' => [
                    'chunk' => $data['chunk'],
                    'offset' => $data['offset'],
                    'clientId' => $data['clientId'],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $res = json_decode($response->getBody(), true);

            if (isset($res['status']) && $res['status'] === 'error') {
                // throw new \Exception($res['message']);
            }

            return $res;
        } catch (\Exception $e) {
            report($e);
            // throw new RedirectExceptions(route('delivery_main'), $e->getMessage());
        }
    }

    function utf8ize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = utf8ize($value);
            }
        } elseif (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'auto');
        }
        return $data;
    }

    public static function postUploadFileVerify($data)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $response = $client->post(self::baseUrl() . '/api/upload/files/verify', [
                'json' => [
                    'total' => $data['total'],
                    'size' => $data['size'],
                    'mimeType' => $data['mimeType'],
                    'fileName' => $data['fileName'],
                    'fileHash' => $data['fileHash'],
                    'type' => $data['type'],
                    'clientId' => $data['clientId'],
                    'metaData' => $data['metaData'],
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $res = json_decode($response->getBody(), true);

            if (isset($res['status']) && $res['status'] === 'error') {
                throw new \Exception($res['message']);
            }

            return $res;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                // エラーレスポンスのステータスコード / ボディは必要に応じてログへ
            } else {
                // レスポンスがない場合
            }
        }
    }

    /**
     * ケースのクローズ状態を更新
     */
    public static function updateGroupClose($groupId, $isClosed)
    {
        self::sessionCheck();
        $adminToken = session('adminToken');

        try {
            $client = new Client();
            $room = self::getGroupDetail($groupId)['data']['group'];

            $response = $client->put(self::baseUrl() . "/api/management/groups/{$groupId}", [
                'json' => [
                    'name' => $room['name'],
                    'avatarFileId' => $room['avatarFileId'] ?? 0,
                    'public' => $room['public'] ?? false,
                    'isClosed' => $isClosed,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('main'), $e->getMessage());
        }
    }

    /**
     * キュー用：adminTokenを直接取得して返す
     */
    public static function getAdminToken()
    {
        try {
            $client = new Client();
            $response = $client->post(self::baseUrl() . '/api/management/auth', [
                'json' => [
                    'username' => self::adminUsername(),
                    'password' => self::adminPassword(),
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data['data']['token'] ?? '';
        } catch (\Exception $e) {
            \Log::error('Admin token acquisition failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * キュー用：workplaceIdを明示的に渡せるdeleteUser
     */
    public static function deleteUserWithToken($userId, $adminToken)
    {
        try {
            $client = new Client([
                'timeout' => 180,
                'connect_timeout' => 30,
            ]);

            $response = $client->delete(self::baseUrl() . "/api/management/users/{$userId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Adminaccesstoken' => $adminToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if ($data['status'] === 'error') {
                throw new \Exception($data['message']);
            }

            return $data;
        } catch (\Exception $e) {
            \Log::error('User deletion API failed', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}