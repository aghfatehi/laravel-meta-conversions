<?php

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\Exceptions\InvalidEventDataException;
use Aghfatehi\LaravelMetaConversions\Services\EventBuilder;

it('builds a conversion event with fluent api', function () {
    $event = (new EventBuilder('Purchase'))
        ->value(99.99)
        ->currency('SAR')
        ->contentIds(['100'])
        ->contentType('product')
        ->quantity(1)
        ->build();

    expect($event)->toBeInstanceOf(ConversionEvent::class);
    expect($event->getEventName())->toBe('Purchase');
    expect($event->getValue())->toBe(99.99);
    expect($event->getCurrency())->toBe('SAR');
});

it('generates uuid as default event id', function () {
    $event = (new EventBuilder('PageView'))->build();

    expect($event->getEventId())->not->toBeNull();
});

it('throws exception when event name is empty', function () {
    $this->expectException(InvalidEventDataException::class);

    (new EventBuilder(''))->build();
});

it('throws exception for invalid currency code', function () {
    $this->expectException(InvalidEventDataException::class);

    (new EventBuilder('Purchase'))
        ->currency('INVALID')
        ->build();
});

it('allows setting custom event id', function () {
    $event = (new EventBuilder('Purchase'))
        ->eventId('custom-id-123')
        ->build();

    expect($event->getEventId())->toBe('custom-id-123');
});

it('chains multiple builders correctly', function () {
    $event = (new EventBuilder('AddToCart'))
        ->value(29.99)
        ->currency('USD')
        ->contentIds(['product-1'])
        ->contentName('Test Product')
        ->contentType('product')
        ->quantity(2)
        ->searchString(null)
        ->status(null)
        ->eventSourceUrl('https://example.com/shop')
        ->actionSource('website')
        ->build();

    expect($event->getEventName())->toBe('AddToCart');
    expect($event->getValue())->toBe(29.99);
    expect($event->getCurrency())->toBe('USD');
    expect($event->getContentIds())->toBe(['product-1']);
    expect($event->getContentName())->toBe('Test Product');
    expect($event->getContentType())->toBe('product');
    expect($event->getQuantity())->toBe(2);
    expect($event->getEventSourceUrl())->toBe('https://example.com/shop');
    expect($event->getActionSource())->toBe('website');
});
