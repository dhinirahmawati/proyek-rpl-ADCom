<?php

require_once __DIR__ . '/tests/bootstrap.php';

$testDir = __DIR__ . '/tests/Unit';
$testFiles = glob($testDir . '/*Test.php');

$totalTests = 0;
$totalPassed = 0;
$totalFailed = 0;
$failures = [];

$startTime = microtime(true);

echo "========================================================\n";
echo "           ADCOM JASA - UNIT TEST RUNNER               \n";
echo "========================================================\n\n";

foreach ($testFiles as $file) {
    require_once $file;

    $className = basename($file, '.php');
    if (!class_exists($className)) {
        continue;
    }

    $refClass = new ReflectionClass($className);
    if ($refClass->isAbstract()) {
        continue;
    }

    $methods = $refClass->getMethods(ReflectionMethod::IS_PUBLIC);
    $testMethods = array_filter($methods, function($m) {
        return str_starts_with($m->getName(), 'test');
    });

    if (empty($testMethods)) {
        continue;
    }

    echo "Running {$className}:\n";

    foreach ($testMethods as $method) {
        $methodName = $method->getName();
        $totalTests++;

        $instance = new $className();

        try {
            if (method_exists($instance, 'setUp')) {
                $refClass->getMethod('setUp')->invoke($instance);
            }

            $methodStart = microtime(true);
            $method->invoke($instance);
            $duration = round((microtime(true) - $methodStart) * 1000, 2);

            if (method_exists($instance, 'tearDown')) {
                $refClass->getMethod('tearDown')->invoke($instance);
            }

            $totalPassed++;
            echo "  [PASS] {$methodName} ({$duration}ms)\n";
        } catch (Throwable $e) {
            if (method_exists($instance, 'tearDown')) {
                try {
                    $refClass->getMethod('tearDown')->invoke($instance);
                } catch (Throwable $ignored) {}
            }

            $totalFailed++;
            $failures[] = [
                'class' => $className,
                'method' => $methodName,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            echo "  [FAIL] {$methodName}: {$e->getMessage()}\n";
        }
    }
    echo "\n";
}

$totalTime = round((microtime(true) - $startTime), 3);

echo "--------------------------------------------------------\n";
echo "Summary: {$totalTests} tests, {$totalPassed} passed, {$totalFailed} failed in {$totalTime}s\n";
echo "--------------------------------------------------------\n";

if ($totalFailed > 0) {
    echo "\nFailures Detail:\n";
    foreach ($failures as $idx => $fail) {
        $num = $idx + 1;
        echo "{$num}) {$fail['class']}::{$fail['method']}\n";
        echo "   Message: {$fail['message']}\n";
        echo "   Location: {$fail['file']}:{$fail['line']}\n\n";
    }
    exit(1);
} else {
    echo "\nSTATUS: SUCCESS (Semua Unit Test Lolos 100%)\n";
    exit(0);
}

