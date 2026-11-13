<?php

interface WhatsAppServiceInterface {
    /**
     * Send an OTP message to the specified phone number
     *
     * @param string $phoneNumber Normalized destination phone number
     * @param string $otpCode 6-digit OTP code
     * @param int $expiresMinutes Expiration in minutes
     * @param string $purpose Purpose of the OTP (registration, login, reset_password, verify_phone)
     * @return array ['success' => bool, 'message' => string, 'provider' => string, 'is_simulated' => bool, 'data' => mixed]
     */
    public function sendOtp($phoneNumber, $otpCode, $expiresMinutes = 5, $purpose = 'registration'): array;

    /**
     * Send a general message (e.g. for connection test)
     *
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public function sendMessage($phoneNumber, $message): array;

    /**
     * Get provider name / identifier
     *
     * @return string
     */
    public function getProviderName(): string;

    /**
     * Check if the provider is fully configured with valid non-placeholder credentials
     *
     * @return bool
     */
    public function isConfigured(): bool;
}
