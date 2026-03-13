<?php
namespace App\Http\Controllers;

use App\Exceptions\RedirectExceptions;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserContentsController extends Controller
{
    public function main()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        session()->forget('workplaceId');

        return view('user/user', ['msg' => ""]);
    }

    public function note()
    {
        return view('user/note');
    }

    public static function registrateUser(Request $request)
    {
        $data = $request->only(['kana', 'password', 'email', 'description']);
        $msg = "";

        try {
            $userData = self::findUser($data);

            if ($userData === null) {
                throw new \Exception("該当するユーザーがいません");
            }

            self::validateEmail($data['email']);

            if ($userData->medilineId === null) {
                // 新規登録
                $msg = self::createNewUser($userData, $data);
            } else {
                // メールアドレス更新
                $msg = self::updateUserEmail($userData, $data);
            }

        } catch (\Exception $e) {
            report($e);
            throw new RedirectExceptions(route('user'), $e->getMessage());
        }

        return redirect()->route('user')->with('message', $msg);
    }

    private static function findUser($data)
    {
        $searchData = [
            'kana' => $data['kana'],
            'password' => $data['password'],
            'emailAddress' => $data['email'],
            'description' => $data['description'],
        ];

        $res = MySQLController::SearchUser($searchData);
        return count($res) > 0 ? $res[0] : null;
    }

    private static function validateEmail($email)
    {
        if (preg_match("/^.+@(docomo\.ne\.jp)$/", $email)) {
            throw new \Exception("docomoのメールアドレスは使用できません。");
        }
    }

    private static function createNewUser($userData, $data)
    {
        $team = $userData->team;
        $name = $userData->name;

        $dateString = (new DateTime())->format('Y-m-d'); // MySQL形式

        $apiData = [
            'displayName' => $name,
            'kana' => $userData->kana,
            'emailAddress' => $data['email'],
            'teamId' => $team,
            'validFrom' => $dateString,
            'validTo' => "",
            'description' => $data['description'],
        ];

        $res = MedilineAPIController::postCreateUser($apiData);
        $userData->medilineId = $res['data']['user']['id'];

        $updateData = [
            'id' => $userData->id,
            'team' => $team,
            'name' => $name,
            'kana' => $userData->kana,
            'password' => $userData->password,
            'medilineId' => $userData->medilineId,
            'registred' => true,
        ];

        MySQLController::UpdateUser($updateData);

        return 'ユーザーを登録しました。';
    }

    private static function updateUserEmail($userData, $data)
    {
        $medilineId = (string)$userData->medilineId;
        $res = MedilineAPIController::getUserDetail($medilineId);
        $user = $res['data']['user'];

        $updateData = [
            'id' => $medilineId,
            'displayName' => $user['displayName'],
            'kana' => $user['kana'],
            'emailAddress' => $data['email'],
            'teamId' => $user['team']['id'],
            'validFrom' => $user['validFrom'],
            'validTo' => $user['validTo'],
            'description' => $user['description'],
        ];

        MedilineAPIController::updateUser($updateData);

        return 'メールアドレスを変更しました。';
    }
}