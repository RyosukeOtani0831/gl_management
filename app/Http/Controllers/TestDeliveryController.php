<?php
namespace App\Http\Controllers;

use App\Exceptions\RedirectExceptions;
use App\Http\Controllers\TestFileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

require_once('MainController.php');

class TestDeliveryController extends Controller
{
    /**
     * 配信専用ページのログインページ描画（大分病院専用）
     */
    public function login()
    {
        return view('test_delivery/test_delivery_login', ['msg' => '']);
    }

    /**
     * 配信専用ページのメインページ描画（大分病院専用）
     */
    public function main()
    {
        // ログインチェック
        if (!session('test_delivery_user_id')) {
            return redirect()->route('test_delivery_login')->with('error', 'ログインが必要です。');
        }

        try {
            $groupList = MedilineAPIController::getGroupList(config('constants.test_oita_workplace_id'))['data']['list'];
            return view('test_delivery/test_delivery_main', [
                'groupList' => $groupList,
                'oita_delivery_user_id' => config('constants.test_oita_delivery_user_id'),
            ]);
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('test_delivery_login')->with('error', 'ケース情報の取得に失敗しました。');
        }
    }

    /**
     * 配信専用ページへのログイン処理（大分病院専用）
     */
    public static function loginDeliveryCheck(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        try {
            MedilineAPIController::auth();
            
            // メールアドレスで直接検索（効率的）
            $response = MedilineAPIController::getUserList(
                config('constants.test_oita_workplace_id'), 
                1, 
                false, 
                $email  // メールアドレスで検索
            );
            
            $userList = $response['data']['list'] ?? [];
            $user = !empty($userList) ? $userList[0] : null;

            if (!$user || $user['id'] !== config('constants.test_oita_delivery_user_id')) {
                throw new \Exception("メールアドレスまたはアカウントが正しくありません。");
            }

            if ($password !== config('constants.test_oita_delivery_password')) {
                throw new \Exception("パスワードが正しくありません。");
            }

            // ログイン成功時にセッションに保存（テスト用のキー名）
            session([
                'test_delivery_user_id' => $user['id'],
                'test_delivery_email' => $user['emailAddress'],
            ]);

        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('test_delivery_login'), $e->getMessage());
        }

        return redirect()->route('test_delivery_main');
    }

    /**
     * 配信専用アカウントからメッセージを配信する（大分病院専用）
     */
    public static function deliveryMessage(Request $request)
    {
        // ログインチェック
        if (!session('test_delivery_user_id')) {
            throw new RedirectExceptions(route('test_delivery_login'), 'ログインが必要です。');
        }

        try {
            self::sendMessage($request->roomId, $request->text);

            if ($request->hasFile('file-upload')) {
                TestFileController::upload($request->file('file-upload'), $request->roomId);
            }

        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('test_delivery_main'), $e->getMessage());
        }

        return redirect()->route('test_delivery_main')->with('message', 'メッセージを配信しました。');
    }

    /**
     * メッセージを送信する処理
     */
    private static function sendMessage($roomId, $text)
    {
        $data = [
            'roomId' => $roomId,
            'type' => "text",
            'text' => $text,
            'userId' => config('constants.test_oita_delivery_user_id'),
        ];
        MedilineAPIController::postManagementMessage($data);
    }

    /**
     * ログアウト処理
     */
    public function logout()
    {
        session()->forget(['test_delivery_user_id', 'test_delivery_email']);
        return redirect()->route('test_delivery_login')->with('message', 'ログアウトしました。');
    }
}