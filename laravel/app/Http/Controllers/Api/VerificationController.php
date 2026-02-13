<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SolapiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VerificationController extends Controller
{
    private const CACHE_PREFIX = 'phone_verify:';
    private const TTL_MINUTES = 5;
    private const CODE_LENGTH = 6;

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
    }

    /**
     * Send verification code SMS to the given phone number.
     */
    public function sendCode(Request $request): JsonResponse
    {
        $phone = $request->input('phone');
        if (!is_string($phone) || trim($phone) === '') {
            return response()->json(['error' => '전화번호를 입력해 주세요.'], 400);
        }

        $normalized = $this->normalizePhone($phone);
        if (strlen($normalized) < 10) {
            return response()->json(['error' => '올바른 전화번호를 입력해 주세요.'], 400);
        }

        $code = (string) random_int(10 ** (self::CODE_LENGTH - 1), 10 ** self::CODE_LENGTH - 1);
        $cacheKey = self::CACHE_PREFIX . $normalized;
        Cache::put($cacheKey, $code, now()->addMinutes(self::TTL_MINUTES));

        $result = SolapiService::sendSms($normalized, '[GrapeSEED] 인증번호는 [' . $code . '] 입니다. ' . self::TTL_MINUTES . '분 내에 입력해 주세요.');

        if (!$result['success']) {
            return response()->json(['error' => $result['error'] ?? '인증번호 발송에 실패했습니다.'], 502);
        }

        return response()->json(['success' => true, 'message' => '인증번호가 발송되었습니다.']);
    }

    /**
     * Verify the code for the given phone number.
     */
    public function verify(Request $request): JsonResponse
    {
        $phone = $request->input('phone');
        $code = $request->input('code');

        if (!is_string($phone) || trim($phone) === '' || !is_string($code) || trim($code) === '') {
            return response()->json(['error' => '전화번호와 인증번호를 입력해 주세요.'], 400);
        }

        $normalized = $this->normalizePhone($phone);
        $code = trim($code);
        $cacheKey = self::CACHE_PREFIX . $normalized;

        $stored = Cache::get($cacheKey);
        if ($stored === null) {
            return response()->json(['error' => '인증번호가 만료되었습니다. 다시 발송해 주세요.'], 400);
        }

        if ($stored !== $code) {
            return response()->json(['error' => '인증번호가 일치하지 않습니다.'], 400);
        }

        Cache::forget($cacheKey);
        $verifiedKey = 'phone_verified:' . $normalized;
        Cache::put($verifiedKey, true, now()->addMinutes(10));

        return response()->json(['success' => true, 'message' => '인증이 완료되었습니다.']);
    }
}
