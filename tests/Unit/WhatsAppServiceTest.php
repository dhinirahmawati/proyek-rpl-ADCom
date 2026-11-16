<?php

use PHPUnit\Framework\TestCase;

class WhatsAppServiceTest extends TestCase {
    public function testResolvesMockAdapterExplicitly(): void {
        $adapter = WhatsAppService::getAdapter('mock');

        $this->assertInstanceOf('MockWhatsAppAdapter', $adapter);
        $this->assertTrue($adapter->isConfigured());
        $this->assertStringContainsString('Mock', $adapter->getProviderName());
    }

    public function testMockAdapterSendOtpSuccess(): void {
        $adapter = new MockWhatsAppAdapter();
        $result = $adapter->sendOtp('081234567890', '482910', 5, 'registration');

        $this->assertTrue($result['success']);
        $this->assertEquals('mock', $result['provider']);
        $this->assertTrue($result['is_simulated']);
        $this->assertEquals('081234567890', $result['data']['target']);
    }

    public function testMockAdapterSendMessageSuccess(): void {
        $adapter = new MockWhatsAppAdapter();
        $result = $adapter->sendMessage('081234567890', 'Halo, ini pesan pengujian');

        $this->assertTrue($result['success']);
        $this->assertEquals('mock', $result['provider']);
        $this->assertTrue($result['is_simulated']);
    }

    public function testMockAdapterStoresOtpInSession(): void {
        $adapter = new MockWhatsAppAdapter();
        $adapter->sendOtp('089876543210', '998877', 10, 'login');

        $this->assertNotEmpty($_SESSION['debug_last_otp']);
        $this->assertEquals('089876543210', $_SESSION['debug_last_otp']['phone']);
        $this->assertEquals('998877', $_SESSION['debug_last_otp']['code']);
        $this->assertEquals('login', $_SESSION['debug_last_otp']['purpose']);
    }
}

