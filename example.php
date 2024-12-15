<?php
require 'vendor/autoload.php';

use NixteeClient\NixteeClient;

$client = new NixteeClient();

try {
    // Authenticate and get token
    $token = $client->authorize('user@example.com', 'password123');
    echo "Token: " . $token . PHP_EOL;

    // Example of using train function
    $trainResponse = $client->train(['data' => 'your training data here']);
    echo "Train response: " . print_r($trainResponse, true) . PHP_EOL;

    // Other functions can be used similarly
    // $client->save($modelId, $data);
    // $client->load($modelId);
    // $client->predict($modelId, $inputData);
    // $client->classify($modelId, $inputData);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
