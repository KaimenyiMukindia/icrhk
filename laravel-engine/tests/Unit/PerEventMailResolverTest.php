<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 3) . '/wp-content/plugins/custom-event-registration/includes/class-cer-mail-smtp-resolver.php';

final class PerEventMailResolverTest extends TestCase
{
    public function test_supports_common_providers(): void
    {
        $cases = [
            ['organizer@gmail.com', 'smtp.gmail.com', 587, 'tls', 'ICRHK Events'],
            ['admin@workspace.example.com', 'smtp.gmail.com', 587, 'tls', 'ICRHK Events'],
            ['hello@outlook.com', 'smtp.office365.com', 587, 'tls', 'ICRHK Events'],
            ['mail@yahoo.com', 'smtp.mail.yahoo.com', 587, 'tls', 'ICRHK Events'],
            ['hello@zoho.com', 'smtp.zoho.com', 587, 'tls', 'ICRHK Events'],
            ['tickets@mydomain.org', 'mail.mydomain.org', 587, 'tls', 'ICRHK Events'],
            ['hello@unknown-site.net', 'mail.unknown-site.net', 587, 'tls', 'ICRHK Events'],
        ];

        foreach ($cases as [$email, $expectedHost, $expectedPort, $expectedSecure, $expectedFromName]) {
            $config = \CerMailSmtpResolver::resolve($email, 'ICRHK Events');
            $this->assertSame($expectedHost, $config['host']);
            $this->assertSame($expectedPort, $config['port']);
            $this->assertSame($expectedSecure, $config['secure']);
            $this->assertSame($expectedFromName, $config['from_name']);
        }
    }
}
