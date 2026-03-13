<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    // ログインフォーム表示
    public function inputForm()
    {
        session()->forget('workplaceId');
        return view('main.login', ['msg' => ""]);
    }

    // ログインチェック処理（reCAPTCHA検証追加版）
    public static function loginCheck(Request $request)
    {
        session()->forget('workplaceId');

        // デバッグ情報
        \Log::info('=== Login Debug Start ===');
        \Log::info('All Request Data:', $request->all());
        \Log::info('g-recaptcha-response:', ['value' => $request->input('g-recaptcha-response')]);
        \Log::info('Config site_key:', ['key' => config('services.recaptcha.site_key')]);
        \Log::info('Config secret_key:', ['key' => substr(config('services.recaptcha.secret_key'), 0, 15) . '...']);
        \Log::info('Full secret_key:', ['key' => config('services.recaptcha.secret_key')]);
        
        // $skipRecaptcha = config('services.recaptcha.skip', false);
        
        // if (!$skipRecaptcha) {
        //     $recaptchaResponse = $request->input('g-recaptcha-response');
            
        //     if (empty($recaptchaResponse)) {
        //         \Log::error('reCAPTCHA response is empty!');
        //         return self::returnLoginError("reCAPTCHA認証が必要です。");
        //     }

        //     $secretKey = config('services.recaptcha.secret_key');
            
        //     \Log::info('Sending to Google:', [
        //         'secret' => substr($secretKey, 0, 15) . '...',
        //         'response' => substr($recaptchaResponse, 0, 20) . '...',
        //         'ip' => $request->ip()
        //     ]);
            
        //     try {
        //         $response = Http::post('https://www.google.com/recaptcha/api/siteverify', [
        //             'secret' => $secretKey,
        //             'response' => $recaptchaResponse,
        //             'remoteip' => $request->ip()
        //         ]);

        //         $result = $response->json();
                
        //         \Log::info('Google Response:', $result);

        //         if (!$result['success']) {
        //             \Log::error('reCAPTCHA verification failed:', $result);
        //             return self::returnLoginError("reCAPTCHA認証に失敗しました。もう一度お試しください。");
        //         }
        //     } catch (\Exception $e) {
        //         \Log::error('reCAPTCHA Exception:', ['message' => $e->getMessage()]);
        //         return self::returnLoginError("認証処理でエラーが発生しました。");
        //     }
        // } else {
        //     \Log::info('reCAPTCHA validation skipped for local development');
        // }

        // 既存のログイン処理
        $user = self::getUserFromEmail($request->email);
        
        if (!$user) {
            return self::returnLoginError("このメールアドレスは登録されていません.");
        }

        session(['user' => $user]);
        return redirect()->route('main');
    }
    
    public static function getUserFromEmail($email)
    {
        MedilineAPIController::auth();
        $workplaceList = self::getWorkplaceList();
        
        foreach ($workplaceList as $workplace) {
            if ($workplace['id'] != 16) {
                $user = self::findUserInWorkplace($email, $workplace['id']);
                if ($user) {
                    return $user;
                }
            }
        }

        return null;
    }

    private static function findUserInWorkplace($email, $workplaceId)
    {
        // 全件取得ではなく、メールアドレスで直接検索
        $response = MedilineAPIController::getUserList($workplaceId, 1, false, $email); // ← 第4引数追加
        $userList = $response['data']['list'] ?? [];
        
        // メールアドレスで検索した結果は1件のみのはず
        if (!empty($userList)) {
            $user = $userList[0];
            session(['userId' => $user['id'], 'workplaceId' => $user['workplaceId']]);
            return $user;
        }

        return null;
    }
    
    private static function getUserList($workplaceId)
    {
        $response = MedilineAPIController::getUserList($workplaceId, 1, true); // ← 第3引数追加
        return $response['data']['list'];
    }

    private static function getWorkplaceList()
    {
        $response = MedilineAPIController::getWorkplaceList();
        return $response['data']['workplaces'];
    }

    private static function returnLoginError($msg)
    {
        return view('main.login', ['msg' => $msg]);
    }
}