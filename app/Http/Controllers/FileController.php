<?php
namespace App\Http\Controllers;

use App\Exceptions\RedirectExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

define("THUMB_WIDTH", 256);

class FileController extends Controller
{
    // アップロード処理
    public static function upload($files, $roomId)
    {
        foreach ($files as $file) {
            $file = self::processFileUpload($file);

            // サムネイルが必要ならば作成してアップロード
            $file = self::processThumbnail($file);

            // ファイルをメッセージとして送信
            self::postFileMessage($file, $roomId);
        }
    }

    // チャンクアップロード処理
    private static function processFileUpload($file)
    {
        $file = self::uploadChunk($file);
        $res = self::finalizeUpload($file);
        $file['id'] = $res['data']['file']['id'];
        $file['thumb_id'] = ""; // 初期状態でサムネイルIDは空

        return $file;
    }

    // サムネイル処理
    private static function processThumbnail($file)
    {
        $fileThumbOriginalPath = self::createThumbnailFile($file);

        if ($fileThumbOriginalPath) {
            $fileThumbs = self::getThumbFileInfo($fileThumbOriginalPath);

            $fileThumb = self::uploadChunk($fileThumbs);
            $res = self::finalizeUpload($fileThumb);
            $file['thumb_id'] = $res['data']['file']['id'];

            // サムネイル作成後、ファイルを削除
            if (file_exists($fileThumbOriginalPath)) {
                unlink($fileThumbOriginalPath);
            }
        }

        return $file;
    }

    // チャンクアップロード
    private static function uploadChunk($file)
    {
        $file = self::prepareFile($file);
        $chunkIndex = 0;
        $offset = 0;

        while ($offset < $file['size']) {
            $chunk = self::readFileChunk($file['tmp_name'], $file['chunk_size'], $chunkIndex);
            $data = [
                "chunk" => base64_encode($chunk),
                "offset" => $chunkIndex,
                "clientId" => $file['clientId'],
            ];
            MedilineAPIController::postUploadFile($data);

            $chunkIndex++;
            $offset = $chunkIndex * $file['chunk_size'];
        }

        return $file;
    }

    // ファイルの準備
    private static function prepareFile($file)
    {
        // $file が配列かどうか確認
        if (is_array($file)) {
            return [
                'name' => is_array($file['name']) ? $file['name'][0] : $file['name'],
                'tmp_name' => is_array($file['tmp_name']) ? $file['tmp_name'][0] : $file['tmp_name'],
                'size' => is_array($file['size']) ? $file['size'][0] : $file['size'],
                'error' => is_array($file['error']) ? $file['error'][0] : $file['error'],
                'type' => is_array($file['type']) ? $file['type'][0] : $file['type'],
                'chunk_size' => self::getChunkSize($file['size']), // チャンクサイズ（整数）
                'clientId' => strval(mt_rand(0, 100000000)), // クライアントID（ランダム生成）
            ];
        }
    
        // $file が UploadedFile の場合
        return [
            'name' => $file->getClientOriginalName(), // 元のファイル名
            'tmp_name' => $file->getPathname(), // 一時保存されているパス
            'size' => $file->getSize(), // ファイルサイズ
            'error' => $file->getError(), // エラーコード
            'type' => $file->getMimeType(), // MIMEタイプ
            'chunk_size' => self::getChunkSize($file->getSize()), // チャンクサイズ（整数）
            'clientId' => strval(mt_rand(0, 100000000)), // クライアントID（ランダム生成）
        ];
    }
    
    // アップロードの最終確認
    private static function finalizeUpload($file)
    {
        $totalChunks = ceil($file['size'] / $file['chunk_size']);
        $fileHash = self::hashFile($file['tmp_name']);
        $metaData = self::getMetaData($file['type'], $file['tmp_name']);

        $data = [
            "total" => $totalChunks,
            "size" => $file['size'],
            "mimeType" => $file['type'],
            "fileName" => $file['name'],
            "fileHash" => $fileHash,
            "type" => "image",
            "clientId" => $file['clientId'],
            "metaData" => $metaData,
        ];

        return MedilineAPIController::postUploadFileVerify($data);
    }

    // ファイルメッセージ送信
    private static function postFileMessage($file, $roomId)
    {
        $fileType = self::getFileType($file['type']);
        $data = [
            'roomId' => $roomId,
            'type' => $fileType,
            'text' => "",
            'userId' => config('constants.oita_delivery_user_id'),
            'fileId' => $file['id'],
            'thumbId' => $file['thumb_id'],
        ];

        MedilineAPIController::postManagementMessage($data);
    }

    // チャンクサイズを返す
    private static function getChunkSize($fileSize)
    {
        $ONE_MB = 1024 * 1024;
        $ONE_GB = 1024 * 1024 * 1024;

        return $fileSize > $ONE_GB ? $ONE_MB * 2 : $ONE_MB;
    }

    // メタデータ取得
    private static function getMetaData($fileType, $fileTmpName)
    {
        if (strpos($fileType, 'image/') === 0) {
            $dimensions = getimagesize($fileTmpName);
            return $dimensions ? ['width' => $dimensions[0], 'height' => $dimensions[1]] : null;
        }

        return null;
    }

    // サムネイルファイル作成
    private static function createThumbnailFile($file)
    {
        if (preg_match("/^video\//", $file['type'])) {
            return self::getVideoThumbnail($file['tmp_name']);
        }

        if (preg_match("/^image\//", $file['type'])) {
            return self::generateThumbFile($file['tmp_name']);
        }

        return null;
    }

    // ハッシュ値を取得
    private static function hashFile($filePath)
    {
        $HALF_GB = 512 * 1024 * 1024;
        $fileSize = filesize($filePath);

        return $fileSize < $HALF_GB ? self::hashSmallFile($filePath) : self::hashBigFile($filePath);
    }

    // 小さいファイルをハッシュ化
    private static function hashSmallFile($filePath)
    {
        return hash_file('sha256', $filePath);
    }

    // 大きなファイルをハッシュ化
    private static function hashBigFile($filePath)
    {
        $chunkSize = 1024 * 1024;
        $sha256 = hash_init('sha256');

        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            throw new \Exception("ファイルを開けませんでした: $filePath");
        }

        while (!feof($handle)) {
            $chunk = fread($handle, $chunkSize);
            hash_update($sha256, $chunk);
        }

        fclose($handle);
        return hash_final($sha256);
    }

    // ファイルチャンクを読み込む
    private static function readFileChunk($filePath, $chunkSize, $index, $encode = false)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }

        $fileSize = filesize($filePath);
        $start = $index * $chunkSize;
        $end = min($start + $chunkSize, $fileSize);
        $length = $end - $start;

        $file = fopen($filePath, 'rb');
        fseek($file, $start);

        $chunk = fread($file, $length);
        fclose($file);

        return $encode ? base64_encode($chunk) : $chunk;
    }

    // ファイルタイプを取得
    private static function getFileType($htmlType)
    {
        if (strpos($htmlType, "image/") === 0) return "image";
        if (strpos($htmlType, "audio/") === 0) return "audio";
        if (strpos($htmlType, "video/") === 0) return "video";
        return "file";
    }

    // サムネイル情報を取得
    private static function getThumbFileInfo($filePath)
    {
        if (file_exists($filePath)) {
            $fileInfo = pathinfo($filePath);
            return [
                'name' => [$fileInfo['basename']],
                'size' => [filesize($filePath)],
                'type' => [mime_content_type($filePath)],
                'tmp_name' => [$filePath],
                'error' => [""],
            ];
        }
        return null;
    }

    // 画像のサムネイルを作成
    private static function generateThumbFile($filePath)
    {
        // サムネイルの保存先ディレクトリとファイルパス
        $directoryPath = storage_path('app/public/thumbs'); // 変更: storage_pathを使用

        if (!file_exists($directoryPath)) {
            if (!mkdir($directoryPath, 0755, true)) {
                // ディレクトリ作成に失敗した場合、エラー処理を追加
                throw new \Exception("サムネイルディレクトリの作成に失敗しました: " . $directoryPath);
            }
        }
        
        // pathinfo で拡張子を取得
        $fileInfo = pathinfo($filePath);
        $extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : 'jpg'; // 拡張子がない場合は 'jpg' をデフォルトに設定

        // サムネイルの保存パスに拡張子を付けて保存
        $thumbnailPath = $directoryPath . '/thumb-' . $fileInfo['filename'] . '.' . $extension;
        // $thumbnailPath = $directoryPath . '/thumb-' . basename($filePath);

        // サムネイル用ディレクトリを作成
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        // 画像を読み込む
        $image = imagecreatefromjpeg($filePath); // 他の形式の場合はimagecreatefrompng()やimagecreatefromgif()を使用
        if (!$image) {
            throw new \Exception("画像の読み込みに失敗しました: " . $filePath);
        }
        // オリジナル画像のサイズ
        list($width, $height) = getimagesize($filePath);
    
        // 新しいサムネイルのサイズ設定
        $thumbWidth = THUMB_WIDTH;
        $thumbHeight = ($thumbWidth / $width) * $height; // アスペクト比を維持
    
        // 新しい画像のリソースを作成
        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    
        // サムネイル画像をリサイズ
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
    
        // サムネイルを保存
        if (!imagejpeg($thumb, $thumbnailPath, 90)) {
            throw new \Exception("サムネイルの保存に失敗しました: " . $thumbnailPath);
        }
    
        // メモリ解放
        imagedestroy($image);
        imagedestroy($thumb);
    
        return $thumbnailPath;
    }
    
    // 動画のサムネイルを作成
    private static function getVideoThumbnail($filePath)
    {
        $thumbnailPath = storage_path('app/public/thumbs/' . basename($filePath) . '.jpg');
    
        // stderr を取得
        $output = [];
        $resultCode = 0;
        exec("ffmpeg -i $filePath -ss 00:00:01.000 -vframes 1 $thumbnailPath 2>&1", $output, $resultCode);
    
        // // 詳細なエラー情報をログに記録
        // Log::error("FFMPEG failed to generate thumbnail", [
        //     'filePath' => $filePath,
        //     'thumbnailPath' => $thumbnailPath,
        //     'output' => $output,
        //     'resultCode' => $resultCode
        // ]);
    
        if ($resultCode !== 0) {
            // エラーが発生した場合、詳細なエラーメッセージを表示
            return null;
        }
    
        return $thumbnailPath;
    }
}