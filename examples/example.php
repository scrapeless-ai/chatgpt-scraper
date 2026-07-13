<?php
/**
 * ChatGPT Scraper — Scrapeless LLM Chat Scraper (PHP example)
 *
 * Docs:  https://docs.scrapeless.com/en/llm-chat-scraper/quickstart/introduction/
 * Token: https://app.scrapeless.com/passport/login?redirect=/quick-start
 *
 * Run (requires the php-curl extension):
 *   export SCRAPELESS_API_TOKEN="your_api_token"
 *   php example.php
 */

$apiUrl = "https://api.scrapeless.com/api/v2/scraper/execute";
$apiToken = getenv("SCRAPELESS_API_TOKEN") ?: "YOUR_API_TOKEN";

$payload = [
    "actor" => "scraper.chatgpt",
    "input" => [
        "prompt"     => "Most reliable proxy service for data extraction",
        "country"    => "US",
        "web_search" => true,
        "shopping"   => false,
    ],
    // Optional: receive the result via webhook instead of the sync response.
    // "webhook" => ["url" => "https://www.your-webhook.com"],
];

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_TIMEOUT        => 180,
    CURLOPT_HTTPHEADER     => [
        "Content-Type: application/json",
        "x-api-token: " . $apiToken,
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response = curl_exec($ch);
if ($response === false) {
    fwrite(STDERR, "Request failed: " . curl_error($ch) . PHP_EOL);
    exit(1);
}

$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($status >= 300) {
    fwrite(STDERR, "Request failed: HTTP {$status}\n{$response}\n");
    exit(1);
}

$data = json_decode($response, true);
$result = $data["task_result"] ?? [];

echo "Status:  " . ($data["status"] ?? "") . PHP_EOL;
echo "Task ID: " . ($data["task_id"] ?? "") . PHP_EOL;
echo "Model:   " . ($result["model"] ?? "") . PHP_EOL;
echo PHP_EOL . "Answer:" . PHP_EOL . ($result["result_text"] ?? "") . PHP_EOL;

echo PHP_EOL . "Raw response:" . PHP_EOL;
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
