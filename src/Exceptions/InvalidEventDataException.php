<?php

namespace Aghfatehi\LaravelMetaConversions\Exceptions;

class InvalidEventDataException extends FacebookConversionException
{
    public static function missingEventName(): self
    {
        return new self('Event name is required.');
    }

    public static function invalidCurrency(string $currency): self
    {
        return new self("Invalid currency code '{$currency}'. Must be a 3-letter ISO 4217 currency code.");
    }
}
