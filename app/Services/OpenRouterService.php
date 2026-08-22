<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    private string $apiKey;

    private string $model;

    private string $baseUrl;

    private array $fallbackModels = [
        'nvidia/nemotron-3-super-120b-a12b:free',
        'nvidia/nemotron-3-nano-30b-a3b:free',
        'nvidia/nemotron-nano-9b-v2:free',
    ];

    public function __construct()
    {
        $this->apiKey = (string) config('services.openrouter.api_key');
        $this->model = (string) config('services.openrouter.model');
        $this->baseUrl = rtrim((string) config('services.openrouter.base_url'), '/');
    }

    public function chat(array $messages, int $maxTokens = 1500): string
    {
        $models = array_merge([$this->model], $this->fallbackModels);
        $lastError = null;

        foreach ($models as $modelIndex => $currentModel) {
            $attempts = $modelIndex === 0 ? 2 : 1;

            for ($attempt = 1; $attempt <= $attempts; $attempt++) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                        'HTTP-Referer' => (string) config('app.url'),
                        'X-Title' => (string) config('app.name', 'AI Job Board'),
                    ])
                        ->withOptions($this->httpOptions())
                        ->timeout(20)
                        ->retry(0)
                        ->post($this->baseUrl . '/chat/completions', [
                            'model' => $currentModel,
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

                    $status = $response->status();
                    $lastError = "OpenRouter ({$currentModel}) failed ({$status})";

                    Log::warning("Chatbot API: {$lastError}", [
                        'model' => $currentModel,
                        'attempt' => $attempt,
                        'response' => $response->body(),
                    ]);

                    if ($status === 429 && $attempt < $attempts) {
                        usleep(1000000);
                    }
                } catch (\Throwable $e) {
                    $lastError = "Chatbot API exception: {$e->getMessage()}";
                    Log::warning('Chatbot API exception', [
                        'model' => $currentModel,
                        'attempt' => $attempt,
                        'error' => $e->getMessage(),
                    ]);

                    if ($attempt < $attempts) {
                        usleep(1000000);
                    }
                }
            }

            if ($modelIndex < count($models) - 1) {
                Log::info("Chatbot API: trying fallback model: {$this->fallbackModels[$modelIndex]}");
            }
        }

        throw new \RuntimeException($lastError ?? 'All AI models failed');
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

        Log::warning('No CA certificate bundle found, SSL verification disabled for OpenRouter.');

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
