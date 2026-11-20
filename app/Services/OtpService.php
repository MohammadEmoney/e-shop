<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Facades\Log;

/**
 * Generates and verifies OTP codes for phone-based customer authentication.
 *
 * Codes are stored (hashed) in the otp_codes table. In production, send the
 * plaintext code via a Persian SMS provider (Kavenegar, Ghasedak, ...). In
 * development the plaintext code is logged so tests/local flows can use it.
 */
class OtpService
{
    /**
     * How long (in minutes) a code remains valid.
     */
    public const TTL_MINUTES = 5;

    /**
     * Maximum verification attempts before a code is invalidated.
     */
    public const MAX_ATTEMPTS = 5;

    /**
     * Generate and persist a fresh OTP for the given phone number.
     */
    public function send(string $phone): string
    {
        // Invalidate any previously unused codes for this phone.
        OtpCode::query()
            ->where('phone', $phone)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $plain = (string) random_int(100000, 999999);

        OtpCode::create([
            'phone' => $phone,
            'code' => bcrypt($plain),
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
            'attempts' => 0,
        ]);

        $this->deliver($phone, $plain);

        return $plain;
    }

    /**
     * Verify a code submitted by the user.
     *
     * @return bool true when the code is valid and not yet consumed
     */
    public function verify(string $phone, string $submitted): bool
    {
        $otp = OtpCode::query()
            ->where('phone', $phone)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (! $otp) {
            return false;
        }

        if (! $otp->isValid()) {
            return false;
        }

        if (! password_verify($submitted, $otp->code)) {
            $otp->increment('attempts');
            if ($otp->attempts >= self::MAX_ATTEMPTS) {
                $otp->update(['used_at' => now()]);
            }

            return false;
        }

        $otp->update(['used_at' => now()]);

        return true;
    }

    /**
     * Deliver the plaintext code to the customer via SMS.
     *
     * In development this logs the code. Swap this for a Kavenegar/Ghasedak
     * API call in production.
     */
    protected function deliver(string $phone, string $plain): void
    {
        Log::info('OTP for '.$phone.' => '.$plain);

        // Production example (Ghasedak / Kavenegar):
        // $api->sendSimple($phone, 'کد ورود شما: '.$plain);
    }
}
