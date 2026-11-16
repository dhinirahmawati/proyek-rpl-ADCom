<?php

use PHPUnit\Framework\TestCase;

class FlasherTest extends TestCase {
    protected function setUp(): void {
        unset($_SESSION['flash']);
    }

    protected function tearDown(): void {
        unset($_SESSION['flash']);
    }

    public function testSetFlashTwoParameters(): void {
        Flasher::setFlash('Operasi berhasil dijalankan', 'success');

        $this->assertNotEmpty($_SESSION['flash']);
        $this->assertEquals('Operasi berhasil dijalankan', $_SESSION['flash']['pesan']);
        $this->assertEquals('', $_SESSION['flash']['aksi']);
        $this->assertEquals('success', $_SESSION['flash']['tipe']);
    }

    public function testSetFlashThreeParameters(): void {
        Flasher::setFlash('Data Pesanan', 'berhasil disimpan', 'primary');

        $this->assertNotEmpty($_SESSION['flash']);
        $this->assertEquals('Data Pesanan', $_SESSION['flash']['pesan']);
        $this->assertEquals('berhasil disimpan', $_SESSION['flash']['aksi']);
        $this->assertEquals('primary', $_SESSION['flash']['tipe']);
    }

    public function testFlashOutputsAlertAndClearsSession(): void {
        Flasher::setFlash('Peringatan sistem', 'warning');

        ob_start();
        Flasher::flash();
        $output = ob_get_clean();

        $this->assertStringContainsString('alert-warning', $output);
        $this->assertStringContainsString('Peringatan sistem', $output);
        $this->assertFalse(isset($_SESSION['flash']));
    }

    public function testFlashDoesNothingWhenSessionFlashNotSet(): void {
        ob_start();
        Flasher::flash();
        $output = ob_get_clean();

        $this->assertEquals('', $output);
    }
}

