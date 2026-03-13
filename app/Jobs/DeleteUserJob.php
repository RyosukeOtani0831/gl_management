<?php

namespace App\Jobs;

use App\Events\DeleteStatusUpdated;
use App\Traits\Dispatchable;
use App\Http\Controllers\MedilineAPIController;
use App\Http\Controllers\MySQLController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Exception\ServerException;

class DeleteUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $backoff = 60;

    protected $userId;
    protected $workplaceId;

    public function __construct($userId, $workplaceId)
    {
        $this->userId = $userId;
        $this->workplaceId = $workplaceId;
    }

    public function handle()
    {
        \Log::info("DeleteUserJob started", ['user_id' => $this->userId]);
        
        try {
            \Log::info("Getting admin token...");
            $adminToken = MedilineAPIController::getAdminToken();
            \Log::info("Admin token acquired");
            
            \Log::info("Deleting user via API...", ['user_id' => $this->userId]);
            
            try {
                $response = MedilineAPIController::deleteUserWithToken($this->userId, $adminToken);
                \Log::info("User deleted via API", ['response' => $response]);
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                // 504タイムアウトでも、削除自体は成功している可能性が高い
                if ($e->getResponse()->getStatusCode() === 504) {
                    \Log::warning("504 timeout but deletion likely succeeded", [
                        'user_id' => $this->userId
                    ]);
                    // 504は正常系として扱う
                } else {
                    throw $e; // 504以外のサーバーエラーは再スロー
                }
            }

            // MySQL側も削除
            \Log::info("Searching MySQL...");
            $res = MySQLController::SearchMedilineUser($this->userId);
            
            if (!empty($res) && isset($res['id'])) {
                \Log::info("Deleting from MySQL...", ['mysql_id' => $res['id']]);
                MySQLController::DeleteUser(['id' => $res['id']]);
            }

            \Log::info("User deleted successfully", ['user_id' => $this->userId]);

        } catch (\Exception $e) {
            \Log::error("User deletion exception", [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}