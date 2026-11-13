<?php
require 'app/init.php';
require 'app/models/UserModel.php';

$model = new UserModel();
$data = [
    'name' => 'Test User',
    'email' => 'test' . time() . '@test.com',
    'password' => '123456'
];

try {
    $res = $model->registerCustomer($data);
    echo "Result: " . $res . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
