<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class MySQLController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // セッションチェック
    private static function sessionCheck()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ユーザー情報を取得
    private static function mapUserData($results)
    {
        return $results->map(function ($res) {
            return (object)[
                'id' => $res->id,
                'name' => $res->name,
                'kana' => $res->kana,
                'password' => $res->password,
                'team' => $res->team,
                'registred' => $res->registred,
                'deleted' => $res->deleted,
                'workplaceId' => $res->workplace_id,
                'medilineId' => $res->mediline_id,
            ];
        });
    }

    // 全ユーザーを取得
    public static function SelectUserAll()
    {
        self::sessionCheck();

        $workplaceId = session('workplaceId');

        $results = DB::table('user')
            ->where('workplace_id', $workplaceId)
            ->where('deleted', false)
            ->where('registred', false)
            ->get();

        return self::mapUserData($results);
    }

    // ユーザー検索
    public static function SearchUser($data)
    {
        self::sessionCheck();

        $kana = str_replace([" ", "　"], "", $data['kana']);
        $pass = str_replace([" ", "　"], "", $data['password']);

        $results = DB::table('user')
            ->whereRaw('REPLACE(REPLACE(kana, " ", ""), "　", "") = ?', [$kana])
            ->whereRaw('REPLACE(REPLACE(password, " ", ""), "　", "") = ?', [$pass])
            ->where('deleted', false)
            ->get();

        return self::mapUserData($results);
    }

    // ユーザーの挿入
    public static function InsertUser($data)
    {
        self::sessionCheck();

        $workplaceId = session('workplaceId');

        $userId = DB::table('user')->insertGetId([
            'name' => $data['name'],
            'kana' => $data['kana'],
            'password' => $data['password'],
            'team' => (int)$data['team'],
            'registred' => false,
            'deleted' => false,
            'workplace_id' => $workplaceId,
        ]);

        return DB::table('user')->where('id', $userId)->first();
    }

    // ユーザーの更新
    public static function UpdateUser($data)
    {
        self::sessionCheck();

        $id = $data['id'];
        $medilineId = $data['medilineId'] ?? null;
        $team = $data['team'];

        $query = DB::table('user')
            ->where('id', $id)
            ->update([
                'name' => $data['name'],
                'kana' => $data['kana'],
                'password' => $data['password'],
                'registred' => (bool)$data['registred'],
                'team' => $team > 0 ? $team : null,  // チームIDが0より大きい場合のみ更新
                'mediline_id' => $medilineId,
                'updated_at' => now(),
            ]);

        return DB::table('user')->where('id', $id)->first();
    }

    // ユーザーの削除
    public static function DeleteUser($data)
    {
        self::sessionCheck();

        $id = $data['id'];

        DB::table('user')
            ->where('id', $id)
            ->update(['deleted' => true, 'updated_at' => now()]);

        return DB::table('user')->where('id', $id)->first();
    }

    // MedilineIDでユーザーを検索
    public static function SearchMedilineUser($medilineId)
    {
        self::sessionCheck();

        $results = DB::table('user')
            ->where('mediline_id', $medilineId)
            ->get();

        return self::mapUserData($results);
    }

    // 管理者コメントを追加
    public static function createAuthComment($mediline_id, $comment)
    {
        $comment = $comment ?? ''; // コメントが空の場合はデフォルト値をセット

        DB::table('auth_comment')->insert([
            'mediline_id' => $mediline_id,
            'comment' => $comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // 管理者コメントを更新
    public static function updateAuthComment($mediline_id, $comment)
    {
        $comment = $comment ?? ''; // コメントが空の場合はデフォルト値をセット

        DB::table('auth_comment')
            ->where('mediline_id', $mediline_id)
            ->update([
                'comment' => $comment,
                'updated_at' => now(),
            ]);
    }

    // MedilineIDでコメントを取得
    public static function getAuthCommentByMedilineId($mediline_id)
    {
        return DB::table('auth_comment')
            ->where('mediline_id', $mediline_id)
            ->first(); // 1件だけ取得
    }
}