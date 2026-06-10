<?php

use Aghfatehi\LaravelMetaConversions\Facades\FacebookConversion;

beforeEach(function () {
    config()->set('facebook-conversion-service.pixel_id', '123456');
    config()->set('facebook-conversion-service.access_token', 'test-token');
    config()->set('facebook-conversion-service.enabled', true);
    config()->set('facebook-conversion-service.logging_enabled', false);

    Http::fake([
        'graph.facebook.com/*' => Http::response(['events_received' => 1], 200),
    ]);
});

it('sends page view event', function () {
    $result = FacebookConversion::pageView();

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('PageView');
});

it('sends view content event', function () {
    $result = FacebookConversion::viewContent(
        productId: 100,
        productName: 'iPhone 15',
        category: 'Phones',
        value: 999,
        currency: 'SAR',
    );

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('ViewContent');
});

it('sends search event', function () {
    $result = FacebookConversion::search('iphone');

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('Search');
});

it('sends add to cart event', function () {
    $result = FacebookConversion::addToCart(
        productId: 100,
        productName: 'iPhone 15',
        quantity: 1,
        value: 999,
        currency: 'SAR',
    );

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('AddToCart');
});

it('sends remove from cart event', function () {
    $result = FacebookConversion::removeFromCart(
        productId: 100,
        productName: 'iPhone 15',
        quantity: 1,
        value: 999,
        currency: 'SAR',
    );

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('RemoveFromCart');
});

it('sends add to wishlist event', function () {
    $result = FacebookConversion::addToWishlist(
        productId: 100,
        productName: 'iPhone 15',
        value: 999,
        currency: 'SAR',
    );

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('AddToWishlist');
});

it('sends initiate checkout event', function () {
    $result = FacebookConversion::initiateCheckout(
        value: 999,
        currency: 'SAR',
        productIds: ['100', '101'],
    );

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('InitiateCheckout');
});

it('sends add payment info event', function () {
    $result = FacebookConversion::addPaymentInfo(
        value: 999,
        currency: 'SAR',
    );

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('AddPaymentInfo');
});

it('sends purchase event', function () {
    $result = FacebookConversion::purchase(
        value: 999,
        currency: 'SAR',
        productIds: ['100', '101'],
    );

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('Purchase');
});

it('sends lead event', function () {
    $result = FacebookConversion::lead(
        value: 50,
        currency: 'USD',
    );

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('Lead');
});

it('sends complete registration event', function () {
    $result = FacebookConversion::completeRegistration(method: 'email');

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('CompleteRegistration');
});

it('sends contact event', function () {
    $result = FacebookConversion::contact();

    expect($result)->not->toBeNull();
    expect($result->getEventName())->toBe('Contact');
});

it('returns null when disabled', function () {
    config()->set('facebook-conversion-service.enabled', false);

    $result = FacebookConversion::pageView();

    expect($result)->toBeNull();
});

it('generates event id for deduplication', function () {
    $result = FacebookConversion::purchase(
        value: 100,
        currency: 'USD',
    );

    expect($result->getEventId())->not->toBeNull();
});

it('uses event builder pattern through facade', function () {
    $event = FacebookConversion::event()
        ->name('Purchase')
        ->value(100)
        ->currency('SAR')
        ->build();

    expect($event->getEventName())->toBe('Purchase');
    expect($event->getValue())->toBe(100.0);
    expect($event->getCurrency())->toBe('SAR');
});
