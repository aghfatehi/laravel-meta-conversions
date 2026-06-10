<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Aghfatehi\LaravelMetaConversions\Services\EventBuilder event()
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null pageView(\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null viewContent(string|int $productId, string $productName, ?string $category = null, ?float $value = null, ?string $currency = null, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null search(string $query, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null addToWishlist(string|int $productId, string $productName, ?float $value = null, ?string $currency = null, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null addToCart(string|int $productId, string $productName, int $quantity = 1, ?float $value = null, ?string $currency = null, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null removeFromCart(string|int $productId, string $productName, int $quantity = 1, ?float $value = null, ?string $currency = null, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null initiateCheckout(?float $value = null, ?string $currency = null, ?array $productIds = null, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null addPaymentInfo(?float $value = null, ?string $currency = null, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null purchase(float $value, string $currency, array $productIds = [], ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null lead(?float $value = null, ?string $currency = null, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null completeRegistration(?string $method = null, ?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static \Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent|null contact(?\Aghfatehi\LaravelMetaConversions\DTOs\UserData $userData = null)
 * @method static array sendEvent(\Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent $event)
 * @method static bool isEnabled()
 *
 * @see \Aghfatehi\LaravelMetaConversions\Services\FacebookConversionService
 */
class FacebookConversion extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'facebook-conversion-service';
    }
}
