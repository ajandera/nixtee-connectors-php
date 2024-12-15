<?php

namespace NixteeClient;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class NixteeClient
{
    private $apiUrl = 'https://api.nixtee.com'; // Placeholder URL for the API
    private $token;

    public function __construct($apiUrl = null)
    {
        if ($apiUrl) {
            $this->apiUrl = $apiUrl;
        }
    }

    // Authorization function
    public function authorize($email, $password)
    {
        $url = $this->apiUrl . '/auth/login';
        $data = [
            'email' => $email,
            'password' => $password,
        ];

        $response = $this->sendRequest('POST', $url, $data);

        if (isset($response['token'])) {
            $this->token = $response['token'];
            return $this->token;
        }

        throw new Exception("Authorization failed.");
    }

    // Train function
    public function train($modelData)
    {
        return $this->authorizedRequest('POST', '/train', $modelData);
    }

    // Save function
    public function save($modelId, $data)
    {
        return $this->authorizedRequest('POST', "/models/$modelId/save", $data);
    }

    // Load function
    public function load($modelId)
    {
        return $this->authorizedRequest('GET', "/models/$modelId/load");
    }

    // Predict function
    public function predict($modelId, $inputData)
    {
        return $this->authorizedRequest('POST', "/models/$modelId/predict", $inputData);
    }

    // Classify function
    public function classify($modelId, $inputData)
    {
        return $this->authorizedRequest('POST', "/models/$modelId/classify", $inputData);
    }

    // Helper function for authorized requests
    private function authorizedRequest($method, $endpoint, $data = null)
    {
        if (!$this->token) {
            throw new Exception("Authorization token is missing. Please login first.");
        }

        $url = $this->apiUrl . $endpoint;
        return $this->sendRequest($method, $url, $data, [
            'Authorization: Bearer ' . $this->token,
        ]);
    }

    // Helper function for making HTTP requests
    private function sendRequest($method, $url, $data = null, $headers = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            throw new Exception("API request failed with response: $response");
        }
    }
}
