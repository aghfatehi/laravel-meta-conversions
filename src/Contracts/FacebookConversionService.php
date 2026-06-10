<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Contracts;

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\DTOs\UserData;
use Aghfatehi\LaravelMetaConversions\Services\EventBuilder;

interface FacebookConversionService
{
    public function event(): EventBuilder;

    public function pageView(?UserData $userData = null): ?ConversionEvent;

    public function viewContent(
        string|int $productId,
        string $productName,
        ?string $category = null,
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function search(
        string $query,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function addToWishlist(
        string|int $productId,
        string $productName,
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function addToCart(
        string|int $productId,
        string $productName,
        int $quantity = 1,
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function removeFromCart(
        string|int $productId,
        string $productName,
        int $quantity = 1,
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function initiateCheckout(
        ?float $value = null,
        ?string $currency = null,
        ?array $productIds = null,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function addPaymentInfo(
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function purchase(
        float $value,
        string $currency,
        array $productIds = [],
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function lead(
        ?float $value = null,
        ?string $currency = null,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function completeRegistration(
        ?string $method = null,
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function contact(
        ?UserData $userData = null,
    ): ?ConversionEvent;

    public function sendEvent(ConversionEvent $event): array;

    public function isEnabled(): bool;
}
