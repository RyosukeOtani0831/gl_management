<?php

namespace App\Services;

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

class SesMailService
{
    private static function client(): SesClient
    {
        return new SesClient([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION', 'ap-northeast-1'),
        ]);
    }

    public static function sendWelcomeMail(string $toAddress, string $userName, string $workplaceName): bool
    {
        $subject = '【外来Law】ご利用開始のご案内';
        $body = self::buildBody($userName, $workplaceName);

        try {
            $client = self::client();
            $client->sendEmail([
                'Destination' => [
                    'ToAddresses' => [$toAddress],
                ],
                'Message' => [
                    'Body' => [
                        'Text' => [
                            'Charset' => 'UTF-8',
                            'Data'    => $body,
                        ],
                    ],
                    'Subject' => [
                        'Charset' => 'UTF-8',
                        'Data'    => $subject,
                    ],
                ],
                'Source' => env('MAIL_FROM_ADDRESS', 'no-reply@gairailaw.com'),
            ]);
            return true;
        } catch (AwsException $e) {
            \Log::error('SES送信エラー: ' . $e->getMessage());
            return false;
        }
    }

    private static function buildBody(string $userName, string $workplaceName): string
    {
        return $userName . " 様\n\nお世話になっております。" . $workplaceName . " です。\nこの度はメールアドレスのご登録をいただき、誠にありがとうございました。\n専用アプリ「外来LAW」の利用準備が整いましたので、本日より本アプリを通じたコミュニケーションを開始させていただきます。\n\n本アプリは、すでにお伝えしております通り、日弁連の基準に準拠した高いセキュリティを備えつつ、LINEとほぼ同様の操作感で手軽にご利用いただけます。\n\n【ログインURL】\nhttps://chat.gairailaw.com/?openExternalBrowser=1\n\n※ログイン時は、ご登録のメールアドレスをご入力いただき、送信される6桁の認証コードをご入力ください。\n※ログイン後、動作確認のため、一度メッセージをお送りいただけますと幸いです。\n\n今後の主な運用については以下の通りです。\n\n1.資料の共有と保存について\n写真や書類などの画像データは、期限による消失がない設定となっております。証拠資料や各種書類の共有も、本アプリから安心してお送りください。\n\n2.事務方との一元化について\n本アプリ内では、弁護士と事務方の双方が内容を確認し、迅速に対応できる体制を整えております。窓口が一元化されることで、より漏れのない円滑な連携が可能となります。\n\n3.今後のご連絡について\n本日以降、当事務所からのご連絡や資料送付は、原則として本アプリを通じて行います。ログイン画面にてアプリ化ボタンを押して頂けますと、新着メッセージが投稿されると通知が表示されますので、ご確認をお願い申し上げます。\n\n安全かつ効率的な課題解決のため、最大限活用してまいる所存です。\n\n" . $workplaceName . "\n\n――――――――――――――――――\n※本メールは送信専用のため、ご返信いただくことができません。\\nサポート・個別のお問い合わせにつきましては、下記メールアドレスまでご連絡ください。\\ngairailaw@64design.jp\\n――――――――――――――――――";
    }
}
