<?php

namespace PHPUnit\Framework;

if (!class_exists('PHPUnit\Framework\TestCase', false)) {
    abstract class TestCase {
        protected function setUp(): void {}
        protected function tearDown(): void {}

        public function assertEquals($expected, $actual, string $message = ''): void {
            if ($expected !== $actual) {
                $expStr = var_export($expected, true);
                $actStr = var_export($actual, true);
                throw new \AssertionError($message ?: "Failed asserting that {$actStr} equals expected {$expStr}.");
            }
        }

        public function assertTrue($condition, string $message = ''): void {
            if ($condition !== true) {
                throw new \AssertionError($message ?: "Failed asserting that condition is true.");
            }
        }

        public function assertFalse($condition, string $message = ''): void {
            if ($condition !== false) {
                throw new \AssertionError($message ?: "Failed asserting that condition is false.");
            }
        }

        public function assertNull($value, string $message = ''): void {
            if ($value !== null) {
                $valStr = var_export($value, true);
                throw new \AssertionError($message ?: "Failed asserting that {$valStr} is null.");
            }
        }

        public function assertNotNull($value, string $message = ''): void {
            if ($value === null) {
                throw new \AssertionError($message ?: "Failed asserting that value is not null.");
            }
        }

        public function assertStringContainsString(string $needle, string $haystack, string $message = ''): void {
            if (strpos($haystack, $needle) === false) {
                throw new \AssertionError($message ?: "Failed asserting that '{$haystack}' contains '{$needle}'.");
            }
        }

        public function assertCount(int $expectedCount, $countable, string $message = ''): void {
            $actualCount = is_countable($countable) ? count($countable) : 0;
            if ($actualCount !== $expectedCount) {
                throw new \AssertionError($message ?: "Failed asserting that count {$actualCount} equals expected {$expectedCount}.");
            }
        }

        public function assertInstanceOf(string $expectedClass, $actual, string $message = ''): void {
            if (!($actual instanceof $expectedClass)) {
                $actClass = is_object($actual) ? get_class($actual) : gettype($actual);
                throw new \AssertionError($message ?: "Failed asserting that {$actClass} is an instance of {$expectedClass}.");
            }
        }

        public function assertNotEmpty($value, string $message = ''): void {
            if (empty($value)) {
                throw new \AssertionError($message ?: "Failed asserting that value is not empty.");
            }
        }
    }
}

