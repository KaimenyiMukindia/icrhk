<?php declare(strict_types=1);

namespace App\Services\WordPress;

final class RegistrationCrypto
{
    private const PREFIX = 'cer:v1:';
    private const CONTEXT = 'cer-registration-pii';

    public static function encrypt(string $value): string
    {
        if ($value === '' || str_starts_with($value, self::PREFIX) || ! function_exists('sodium_crypto_secretbox')) {
            return $value;
        }

        $key = hash_hkdf('sha256', self::keyMaterial(), 32, self::CONTEXT);
        $nonce = random_bytes(24);

        return self::PREFIX . base64_encode($nonce . sodium_crypto_secretbox($value, $nonce, $key));
    }

    public static function decrypt(string $value): string
    {
        if (! str_starts_with($value, self::PREFIX) || ! function_exists('sodium_crypto_secretbox_open')) {
            return $value;
        }

        $decoded = base64_decode(substr($value, strlen(self::PREFIX)), true);
        if ($decoded === false || strlen($decoded) <= 24) {
            return '';
        }

        $key = hash_hkdf('sha256', self::keyMaterial(), 32, self::CONTEXT);
        $plaintext = sodium_crypto_secretbox_open(
            substr($decoded, 24),
            substr($decoded, 0, 24),
            $key
        );

        return $plaintext === false ? '' : $plaintext;
    }

    private static function keyMaterial(): string
    {
        return (string) env('CER_ENCRYPTION_KEY', env('AUTH_KEY', ''));
    }
}