<?php

use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase {
    public function testGetReturnsDefaultWhenKeyNotFound(): void {
        $uniqueKey = 'NON_EXISTENT_KEY_' . time() . '_' . rand(1000, 9999);
        $defaultValue = 'fallback_default_value';

        $result = Env::get($uniqueKey, $defaultValue);
        $this->assertEquals($defaultValue, $result);
    }

    public function testMaskHidesSensitiveStrings(): void {
        $secret = 'mySuperSecretApiKey123456';
        $masked = Env::mask($secret, 4);

        $this->assertStringContainsString('••••••••', $masked);
        $this->assertStringContainsString('3456', $masked);
    }

    public function testMaskShortString(): void {
        $shortSecret = 'abc';
        $masked = Env::mask($shortSecret, 4);

        $this->assertEquals('••••••••', $masked);
    }

    public function testMaskPlaceholderString(): void {
        $this->assertEquals('Belum Dikonfigurasi', Env::mask('ISI_API_KEY_DI_ENV'));
        $this->assertEquals('Belum Dikonfigurasi', Env::mask('ISI_TOKEN_DI_ENV'));
    }

    public function testMaskEmptyString(): void {
        $this->assertEquals('Belum Dikonfigurasi', Env::mask(''));
        $this->assertEquals('Belum Dikonfigurasi', Env::mask(null));
    }
}

