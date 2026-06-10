<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Services;

use Aghfatehi\LaravelMetaConversions\Contracts\FacebookConversionService as FacebookConversionServiceContract;
use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\DTOs\UserData;

class FacebookConversionService implements FacebookConversionServiceContract
{
    public function __construct(
        private readonly MetaConversionApiClient $client,
    ) {}

    public function isEnabled(): bool
    {
        return (bool) config('facebook-conversion-service.enabled', true);
    }

    public function event(): EventBuilder
    {
        return new EventBuilder('');
    }

    public function pageView(?UserData $userData = null): ?ConversionEvent
    {
        return $this->sendStandardEvent('PageView', userData: $userData);
    }

    public function viewContent(
        string|int $productId,
        string $productName,
        ?string $category = null,
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('ViewContent', [
            'content_ids' => [(string) $productId],
            'content_name' => $productName,
            'content_category' => $category,
            'content_type' => 'product',
            'value' => $value,
            'currency' => $currency,
        ], $userData);
    }

    public function search(
        string $query,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('Search', [
            'search_string' => $query,
        ], $userData);
    }

    public function addToWishlist(
        string|int $productId,
        string $productName,
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('AddToWishlist', [
            'content_ids' => [(string) $productId],
            'content_name' => $productName,
            'content_type' => 'product',
            'value' => $value,
            'currency' => $currency,
        ], $userData);
    }

    public function addToCart(
        string|int $productId,
        string $productName,
        int $quantity = 1,
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('AddToCart', [
            'content_ids' => [(string) $productId],
            'content_name' => $productName,
            'content_type' => 'product',
            'quantity' => $quantity,
            'value' => $value,
            'currency' => $currency,
        ], $userData);
    }

    public function removeFromCart(
        string|int $productId,
        string $productName,
        int $quantity = 1,
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('RemoveFromCart', [
            'content_ids' => [(string) $productId],
            'content_name' => $productName,
            'content_type' => 'product',
            'quantity' => $quantity,
            'value' => $value,
            'currency' => $currency,
        ], $userData);
    }

    public function initiateCheckout(
        ?float $value = null,
        ?string $currency = null,
        ?array $productIds = null,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('InitiateCheckout', [
            'value' => $value,
            'currency' => $currency,
            'content_ids' => $productIds,
            'content_type' => $productIds !== null ? 'product' : null,
        ], $userData);
    }

    public function addPaymentInfo(
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('AddPaymentInfo', [
            'value' => $value,
            'currency' => $currency,
        ], $userData);
    }

    public function purchase(
        float $value,
        string $currency,
        array $productIds = [],
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('Purchase', [
            'value' => $value,
            'currency' => $currency,
            'content_ids' => $productIds,
            'content_type' => !empty($productIds) ? 'product' : null,
        ], $userData);
    }

    public function lead(
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('Lead', [
            'value' => $value,
            'currency' => $currency,
        ], $userData);
    }

    public function completeRegistration(
        ?string $method = null,
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('CompleteRegistration', [
            'status' => $method,
        ], $userData);
    }

    public function contact(
        ?UserData $userData = null,
    ): ?ConversionEvent {
        return $this->sendStandardEvent('Contact', userData: $userData);
    }

    public function sendEvent(ConversionEvent $event): array
    {
        if (!$this->isEnabled()) {
            return [];
        }

        $queueEnabled = (bool) config('facebook-conversion-service.queue_enabled', false);

        if ($queueEnabled) {
            $job = new \Aghfatehi\LaravelMetaConversions\Jobs\SendFacebookEventJob($event);
            $connection = config('facebook-conversion-service.queue_connection');

            if ($connection !== null) {
                $job->onConnection($connection);
            }

            dispatch($job);

            return [];
        }

        return $this->client->sendEvent($event);
    }

    private function sendStandardEvent(string $eventName, array $customData = [], ?UserData $userData = null): ?ConversionEvent
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $builder = (new EventBuilder($eventName))
            ->userData($userData ?? $this->buildDefaultUserData())
            ->eventSourceUrl(request()->url());

        foreach ($customData as $key => $value) {
            if ($value === null) {
                continue;
            }

            match ($key) {
                'value' => $builder->value((float) $value),
                'currency' => $builder->currency($value),
                'content_ids' => $builder->contentIds($value),
                'content_type' => $builder->contentType($value),
                'content_name' => $builder->contentName($value),
                'content_category' => $builder->contentCategory($value),
                'quantity' => $builder->quantity((int) $value),
                'search_string' => $builder->searchString($value),
                'status' => $builder->status($value),
                default => null,
            };
        }

        $event = $builder->build();

        $this->sendEvent($event);

        return $event;
    }

    private function buildDefaultUserData(): UserData
    {
        $userData = new UserData;

        if ($user = auth()->user()) {
            $userData
                ->email($user->email)
                ->phone($user->phone ?? null)
                ->firstName($user->first_name ?? $user->name ?? null)
                ->lastName($user->last_name ?? null);
        }

        $userData
            ->fbp(request()->cookie('_fbp'))
            ->fbc(request()->cookie('_fbc'));

        return $userData;
    }
}
