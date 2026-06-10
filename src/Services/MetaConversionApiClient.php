<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Services;

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\Exceptions\FacebookConversionException;
use Aghfatehi\LaravelMetaConversions\Exceptions\InvalidConfigException;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class MetaConversionApiClient
{
    private const GRAPH_API_URL = 'https://graph.facebook.com';

    private ?string $pixelId;

    private ?string $accessToken;

    private string $apiVersion;

    private int $timeout;

    private int $retryAttempts;

    private int $retryDelay;

    private bool $loggingEnabled;

    private string $logChannel;

    private ?string $testEventCode;

    public function __construct(
        private readonly HttpClient $http,
    ) {
        $this->pixelId = config('facebook-conversion-service.pixel_id');
        $this->accessToken = config('facebook-conversion-service.access_token');
        $this->apiVersion = config('facebook-conversion-service.api_version', 'v21.0');
        $this->timeout = (int) config('facebook-conversion-service.timeout', 5);
        $this->retryAttempts = (int) config('facebook-conversion-service.retry_attempts', 3);
        $this->retryDelay = (int) config('facebook-conversion-service.retry_delay', 100);
        $this->loggingEnabled = (bool) config('facebook-conversion-service.logging_enabled', true);
        $this->logChannel = config('facebook-conversion-service.log_channel', 'facebook_conversion');
        $this->testEventCode = config('facebook-conversion-service.test_event_code');
    }

    public function validateConfig(): void
    {
        if (empty($this->pixelId)) {
            throw InvalidConfigException::missingPixelId();
        }

        if (empty($this->accessToken)) {
            throw InvalidConfigException::missingAccessToken();
        }
    }

    public function sendEvent(ConversionEvent $event): array
    {
        $this->validateConfig();

        $payload = $this->buildPayload($event);

        $this->log('info', 'Sending event to Facebook Conversion API', [
            'event_name' => $event->getEventName(),
            'event_id' => $event->getEventId(),
        ]);

        try {
            $response = $this->sendRequest($payload);

            $body = $response->json() ?? [];

            if ($response->failed()) {
                $this->log('error', 'Facebook Conversion API request failed', [
                    'status' => $response->status(),
                    'response' => $this->maskSensitiveData($body),
                ]);

                throw FacebookConversionException::apiError(
                    $body['error']['message'] ?? 'Unknown API error',
                    $response->status()
                );
            }

            $this->log('info', 'Facebook Conversion API event sent successfully', [
                'response' => $body,
            ]);

            return $body ?? [];
        } catch (RequestException $e) {
            $this->log('error', 'Facebook Conversion API request failed', [
                'status' => $e->response?->status(),
                'response' => $this->maskSensitiveData($e->response?->json() ?? []),
            ]);

            throw FacebookConversionException::apiError(
                $e->response?->json()['error']['message'] ?? $e->getMessage(),
                $e->response?->status() ?? 0
            );
        } catch (\RuntimeException $e) {
            $this->log('error', 'Facebook Conversion API HTTP error', [
                'message' => $e->getMessage(),
            ]);

            throw FacebookConversionException::apiError($e->getMessage());
        }
    }

    private function buildPayload(ConversionEvent $event): array
    {
        $data = $event->toArray();

        $payload = [
            'data' => [$data],
            'access_token' => $this->accessToken,
        ];

        if ($this->testEventCode !== null && $this->testEventCode !== '') {
            $payload['test_event_code'] = $this->testEventCode;
        }

        return $payload;
    }

    private function sendRequest(array $payload): Response
    {
        $url = sprintf('%s/%s/%s/events', self::GRAPH_API_URL, $this->apiVersion, $this->pixelId);

        $request = $this->http->timeout($this->timeout)->retry(
            $this->retryAttempts,
            $this->retryDelay,
        );

        return $request->asJson()->post($url, $payload);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        if (!$this->loggingEnabled) {
            return;
        }

        Log::channel($this->logChannel)->$level($message, $context);
    }

    private function maskSensitiveData(array $data): array
    {
        if (isset($data['access_token'])) {
            $data['access_token'] = substr($data['access_token'], 0, 8) . '****';
        }

        return $data;
    }

    public function getPixelId(): ?string
    {
        return $this->pixelId;
    }
}
