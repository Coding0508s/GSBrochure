<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolapiService
{
    public static function createAuthHeader(string $apiKey, string $apiSecret): string
    {
        $dateTime = gmdate('Y-m-d\TH:i:s\Z');
        $salt = bin2hex(random_bytes(16));
        $data = $dateTime . $salt;
        $signature = hash_hmac('sha256', $data, $apiSecret);
        return "HMAC-SHA256 apiKey={$apiKey}, date={$dateTime}, salt={$salt}, signature={$signature}";
    }

    /**
     * Send a single SMS via Solapi.
     * @param string $to Recipient phone (digits only, e.g. 01012345678)
     * @param string $text Message body
     * @return array{success: bool, error?: string}
     */
    public static function sendSms(string $to, string $text): array
    {
        $apiKey = config('services.solapi.key');
        $apiSecret = config('services.solapi.secret');
        $from = config('services.solapi.from');

        if (empty($apiKey) || empty($apiSecret) || empty($from)) {
            Log::warning('Solapi: missing config (key, secret or from)');
            return ['success' => false, 'error' => '문자 발송 설정이 되어 있지 않습니다.'];
        }

        $to = preg_replace('/\D/', '', $to);
        if (strlen($to) < 10) {
            return ['success' => false, 'error' => '올바른 전화번호를 입력해 주세요.'];
        }

        $authHeader = self::createAuthHeader($apiKey, $apiSecret);
        $body = [
            'message' => [
                'to' => $to,
                'from' => preg_replace('/\D/', '', $from),
                'text' => $text,
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
            ])->post('https://api.solapi.com/messages/v4/send-many/detail', $body);

            if (!$response->successful()) {
                $err = $response->json();
                $message = $err['errorMessage'] ?? $err['error'] ?? '문자 발송에 실패했습니다.';
                Log::warning('Solapi send failed', ['status' => $response->status(), 'body' => $response->body()]);
                return ['success' => false, 'error' => $message];
            }
            return ['success' => true];
        } catch (\Throwable $e) {
            Log::error('Solapi send exception', ['message' => $e->getMessage()]);
            return ['success' => false, 'error' => '문자 발송 중 오류가 발생했습니다.'];
        }
    }
}
