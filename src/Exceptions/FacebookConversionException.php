<?php

namespace Aghfatehi\LaravelMetaConversions\Exceptions;

use Exception;

class FacebookConversionException extends Exception
{
    public static function configMissing(string $key): self
    {
        return new self("Facebook Conversion Service: Missing required configuration '{$key}'.");
    }

    public static function apiError(string $message, int $statusCode = 0): self
    {
        return new self("Facebook Conversion API error: {$message}", $statusCode);
    }
}
