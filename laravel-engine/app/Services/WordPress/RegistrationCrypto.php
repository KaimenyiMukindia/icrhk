<?php declare(strict_types=1);

namespace App\Services\WordPress;

final class RegistrationCrypto
{
    private const PREFIX = 'cer:v1:';
    private const CONTEXT = 'cer-registration-pii';

    public static function encrypt(string $value): string
    {
        if ($value === '' || str_starts_with($value, self::PREFIX)) {
            return $value;
        }

        $key = hash_hkdf('sha256', self::keyMaterial(), \SODIUM_CRYPTO_SECRETBOX_KEYBYTES, self::CONTEXT);
        $nonce = random_bytes(\SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        return self::PREFIX . base64_encode($nonce . sodium_crypto_secretbox($value, $nonce, $key));
    }

    public static function decrypt(string $value): string
    {
        if (! str_starts_with($value, self::PREFIX)) {
            return $value;
        }

        $decoded = base64_decode(substr($value, strlen(self::PREFIX)), true);
        if ($decoded === false || strlen($decoded) <= \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            return '';
        }

        $key = hash_hkdf('sha256', self::keyMaterial(), \SODIUM_CRYPTO_SECRETBOX_KEYBYTES, self::CONTEXT);
        $plaintext = sodium_crypto_secretbox_open(
            substr($decoded, \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
            substr($decoded, 0, \SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
            $key
        );

        return $plaintext === false ? '' : $plaintext;
    }

    private static function keyMaterial(): string
    {
        return (string) env('CER_ENCRYPTION_KEY', env('AUTH_KEY', ''));
    }
}