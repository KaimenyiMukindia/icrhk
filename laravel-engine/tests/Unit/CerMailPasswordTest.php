<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname(__DIR__, 3) . DIRECTORY_SEPARATOR );
}
if ( ! defined( 'CER_ENCRYPTION_KEY' ) ) {
    define( 'CER_ENCRYPTION_KEY', 'test-encryption-key' );
}

require_once dirname(__DIR__, 3) . '/wp-content/plugins/custom-event-registration/includes/class-cer-security.php';

final class CerMailPasswordTest extends TestCase
{
    public function test_normalizes_only_the_gmail_display_format(): void
    {
        $this->assertSame('abcdefghijklmnop', cer_normalize_mail_password('abcd efgh ijkl mnop'));
        $this->assertSame('abcdefghijklmnop', cer_normalize_mail_password(' abcd efgh ijkl mnop '));
        $this->assertSame('abcdefghijklmnop', cer_normalize_mail_password('abcdefghijklmnop'));
        $this->assertSame('my pass with spaces!', cer_normalize_mail_password('my pass with spaces!'));
        $this->assertSame('p@ss  word#42', cer_normalize_mail_password('  p@ss  word#42  '));
    }

    public function test_encryption_round_trip_preserves_normalized_password(): void
    {
        $input = cer_normalize_mail_password('abcd efgh ijkl mnop');
        $this->assertSame($input, cer_decrypt_pii(cer_encrypt_pii($input)));

        $custom = cer_normalize_mail_password('my pass with spaces!');
        $this->assertSame($custom, cer_decrypt_pii(cer_encrypt_pii($custom)));
    }
}
