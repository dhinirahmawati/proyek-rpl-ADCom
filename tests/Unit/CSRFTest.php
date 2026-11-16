<?php

use PHPUnit\Framework\TestCase;

class CSRFTest extends TestCase {
    protected function setUp(): void {
        unset($_SESSION['csrf_token']);
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    protected function tearDown(): void {
        unset($_SESSION['csrf_token']);
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    public function testGenerateReturns32ByteHexToken(): void {
        $token = CSRF::generate();
        $this->assertNotEmpty($token);
        $this->assertEquals(64, strlen($token));
        $this->assertTrue(ctype_xdigit($token));
    }

    public function testGenerateReusesSessionToken(): void {
        $token1 = CSRF::generate();
        $token2 = CSRF::generate();
        $this->assertEquals($token1, $token2);
    }

    public function testInputGeneratesValidHtmlField(): void {
        $input = CSRF::input();
        $token = $_SESSION['csrf_token'];
        $expected = '<input type="hidden" name="csrf_token" value="' . $token . '">';
        $this->assertEquals($expected, $input);
    }

    public function testVerifySuccessWithValidPostToken(): void {
        $token = CSRF::generate();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = $token;

        $this->assertTrue(CSRF::verify());
    }

    public function testVerifyFailsWithMismatchedToken(): void {
        CSRF::generate();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['csrf_token'] = 'invalid_tampered_token_12345';

        $this->assertFalse(CSRF::verify());
    }

    public function testVerifyFailsWhenPostTokenMissing(): void {
        CSRF::generate();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        unset($_POST['csrf_token']);

        $this->assertFalse(CSRF::verify());
    }

    public function testVerifyReturnsTrueOnGetRequest(): void {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue(CSRF::verify());
    }
}

