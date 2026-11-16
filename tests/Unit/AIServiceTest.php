<?php

use PHPUnit\Framework\TestCase;

class AIServiceTest extends TestCase {
    private array $sampleServices;

    protected function setUp(): void {
        $this->sampleServices = [
            [
                'id' => 1,
                'name' => 'Instalasi CCTV',
                'description' => 'Pemasangan kamera CCTV 4 channel.',
                'estimated_price' => 1500000,
                'estimated_time' => '1-2 Hari',
                'category_name' => 'Security'
            ],
            [
                'id' => 2,
                'name' => 'Service Laptop',
                'description' => 'Pembersihan heatsink, ganti thermal paste.',
                'estimated_price' => 300000,
                'estimated_time' => '2-4 Jam',
                'category_name' => 'Hardware'
            ]
        ];
    }

    public function testAiServiceFallbackReturnsValidGuidanceForServices(): void {
        $messages = [
            ['role' => 'user', 'content' => 'Halo, ada layanan jasa apa saja?']
        ];

        $response = AIService::getResponse($messages, $this->sampleServices, 'Budi');

        $this->assertNotEmpty($response);
        $this->assertStringContainsString('CCTV', $response);
    }

    public function testAiServiceAnswersOrderFlow(): void {
        $messages = [
            ['role' => 'user', 'content' => 'Bagaimana cara memesan layanan servis?']
        ];

        $response = AIService::getResponse($messages, $this->sampleServices, 'Siti');

        $this->assertNotEmpty($response);
        $this->assertTrue(
            strpos($response, 'pesan') !== false ||
            strpos($response, 'Pesanan') !== false ||
            strpos($response, 'order') !== false ||
            strpos($response, 'layanan') !== false
        );
    }

    public function testAiServiceHandlesDisallowedTopicGracefully(): void {
        $messages = [
            ['role' => 'user', 'content' => 'Tuliskan puisi cinta tentang bunga mawar merah']
        ];

        $response = AIService::getResponse($messages, $this->sampleServices, 'Ahmad');

        $this->assertNotEmpty($response);
        $this->assertStringContainsString('Maaf', $response);
    }
}
