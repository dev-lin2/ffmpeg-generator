<?php

namespace App\Services;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LineWorkService
{
    private $clientId;
    private $serverSecret;
    private $serverId;
    private $privateKeyPath;
    private $consumerKey;
    private $botNo;
    private $authUrl;

    public function __construct()
    {
        $this->clientId = config('linework.linework_client_id');
        $this->serverSecret = config('linework.linework_server_secret');
        $this->serverId = config('linework.linework_server_id');
        $this->privateKeyPath = storage_path('app/private.key');
        $this->consumerKey = config('linework.linework_consumer_key');
        $this->botNo = config('linework.linework_bot_no');
        $this->authUrl = config('linework.linework_auth_url');
    }

    public function generateAndSaveToken()
    {
        $jwtToken = $this->getJwt();
        $accessToken = $this->getAccessToken($jwtToken);

        $tokenData = [
            'access_token' => $accessToken['access_token'],
            'expires_in' => $accessToken['expires_in'],
            'token_type' => $accessToken['token_type'],
            'scope' => $accessToken['scope'],
            'generated_at' => time(),
        ];

        Storage::put('token.json', json_encode($tokenData));

        return $tokenData;
    }

    public function sendVideo($userId, $videoUrl, $thumbnailUrl)
    {
        $accessToken = $this->getStoredAccessToken();

        $ch = curl_init();
        $headers = [
            "Authorization: Bearer {$accessToken}",
            "Content-Type: application/json; charset=utf-8",
            "consumerKey: {$this->consumerKey}",
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $url = "https://www.worksapis.com/v1.0/bots/{$this->botNo}/users/{$userId}/messages";
        curl_setopt($ch, CURLOPT_URL, $url);

        $body = [
            "content" => [
                "type" => "image_carousel",
                "columns" => [
                    [
                        "originalContentUrl" => $thumbnailUrl,
                        "action" => [
                            "type" => "uri",
                            "label" => "view",
                            "uri" => $videoUrl
                        ]
                    ]
                ]
            ]
        ];
        $body = json_encode($body);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Log the response
        Log::info("LineWorkService::sendVideo response: " . $res);

        return $httpcode;
    }

    private function getJwt()
    {
        $privateKey = file_get_contents($this->privateKeyPath);

        return JWT::encode([
            "iss" => $this->clientId,
            "sub" => $this->serverId,
            "iat" => time(),
            "exp" => time() + 3600
        ], $privateKey, 'RS256');
    }

    private function getAccessToken($jwtToken)
    {
        $url = $this->authUrl;

        $options = [
            'form_params' => [
                "grant_type" => urlencode("urn:ietf:params:oauth:grant-type:jwt-bearer"),
                "assertion" => $jwtToken,
                "client_id" => $this->clientId,
                "client_secret" => $this->serverSecret,
                "scope" => 'bot,user.read',
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
            ]
        ];

        $client = new Client();
        $response = $client->request("POST", $url, $options);
        $body = $response->getBody();
        return json_decode($body, true);
    }

    private function getStoredAccessToken()
    {
        $tokenJson = Storage::get('token.json');
        $tokenData = json_decode($tokenJson, true);

        if (!$tokenData || (time() - $tokenData['generated_at']) > ($tokenData['expires_in'] - 300)) {
            $newTokenData = $this->generateAndSaveToken();
            return $newTokenData['access_token'];
        }

        return $tokenData['access_token'];
    }

    public function sendGif($userId, $gifFilePath)
    {
        $uploadResponse = $this->uploadAttachment($gifFilePath);
        if (!$uploadResponse || !isset($uploadResponse->uploadUrl)) {
            Log::error("Failed to get upload URL for GIF");
            return false;
        }

        $fileId = $this->uploadFile($uploadResponse->uploadUrl, $gifFilePath);
        if (!$fileId) {
            Log::error("Failed to upload GIF file");
            return false;
        }

        return $this->sendGifMessage($userId, $fileId);
    }

    private function uploadAttachment($filePath)
    {
        $accessToken = $this->getStoredAccessToken();

        $client = new Client();
        $response = $client->post("https://www.worksapis.com/v1.0/bots/{$this->botNo}/attachments", [
            'headers' => [
                "Authorization" => "Bearer {$accessToken}",
                "Content-Type" => "application/json; charset=utf-8",
                "consumerKey" => $this->consumerKey,
            ],
            'json' => [
                "fileName" => basename($filePath),
            ],
        ]);

        return json_decode($response->getBody());
    }

    private function uploadFile($uploadUrl, $filePath)
    {
        $client = new Client();
        $response = $client->post($uploadUrl, [
            'multipart' => [
                [
                    'name'     => 'Filedata',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
            ],
        ]);

        $result = json_decode($response->getBody());
        return $result->fileId ?? null;
    }

    private function sendGifMessage($userId, $fileId)
    {
        $accessToken = $this->getStoredAccessToken();

        $client = new Client();
        $response = $client->post("https://www.worksapis.com/v1.0/bots/{$this->botNo}/users/{$userId}/messages", [
            'headers' => [
                "Authorization" => "Bearer {$accessToken}",
                "Content-Type" => "application/json; charset=utf-8",
                "consumerKey" => $this->consumerKey,
            ],
            'json' => [
                "content" => [
                    "type" => "image",
                    "fileId" => $fileId
                ]
            ],
        ]);

        return $response->getStatusCode();
    }
}
