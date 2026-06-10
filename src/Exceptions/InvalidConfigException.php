<?php

namespace Aghfatehi\LaravelMetaConversions\Exceptions;

class InvalidConfigException extends FacebookConversionException
{
    public static function missingPixelId(): self
    {
        return new self('Facebook Pixel ID is not configured. Set FACEBOOK_PIXEL_ID in your .env file.');
    }

    public static function missingAccessToken(): self
    {
        return new self('Facebook Conversion API token is not configured. Set FACEBOOK_PIXEL_API in your .env file.');
    }
}
