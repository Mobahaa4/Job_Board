<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    private string $apiKey;

    private string $model;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openrouter.api_key');
        $this->model = (string) config('services.openrouter.model');
        $this->baseUrl = rtrim((string) config('services.openrouter.base_url'), '/');
    }

    public function chat(array $messages, int $maxTokens = 500): string
    {
        $lastError = null;

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => (string) config('app.url'),
                'X-Title' => (string) config('app.name', 'AI Job Board'),
            ])
                ->withOptions($this->httpOptions())
                ->timeout(30)
                ->retry(0)
                ->post($this->baseUrl . '/chat/completions', [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => $maxTokens,
                    'temperature' => 0.4,
                    'route' => 'fallback',
                ]);

            if ($response->successful()) {
                $content = data_get($response->json(), 'choices.0.message.content');

                if (is_string($content) && trim($content) !== '') {
                    return trim($content);
                }
            }

            $lastError = 'OpenRouter request failed (' . $response->status() . '): ' . $response->body();

            if ($attempt < 2 && $response->status() === 429) {
                usleep(500000);
            }
        }

        throw new \RuntimeException($lastError);
    }

    private function httpOptions(): array
    {
        if ((bool) config('services.openrouter.verify_ssl', true) === false) {
            return ['verify' => false];
        }

        $caBundle = $this->findCaBundle();
        if ($caBundle !== null) {
            return ['verify' => $caBundle];
        }

        Log::warning('No CA certificate bundle found on this machine, SSL verification disabled for OpenRouter.');

        return ['verify' => false];
    }

    private function findCaBundle(): ?string
    {
        $candidates = array_filter([
            config('services.openrouter.ca_bundle'),
            ini_get('curl.cainfo') ?: null,
            getenv('CURL_CA_BUNDLE') ?: null,
            getenv('SSL_CERT_FILE') ?: null,
            'C:\\xampp\\php\\extras\\ssl\\cacert.pem',
            'D:\\Xampp\\php\\extras\\ssl\\cacert.pem',
            'C:\\wamp64\\bin\\php\\extras\\ssl\\cacert.pem',
            'C:\\laragon\\bin\\php\\cacert.pem',
            'C:\\Users\\' . getenv('USERNAME') . '\\.cacert\\cacert.pem',
        ]);

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }
}
