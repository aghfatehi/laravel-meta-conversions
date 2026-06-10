<?php

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\DTOs\UserData;

it('creates a conversion event with required fields', function () {
    $event = new ConversionEvent(
        eventName: 'Purchase',
        eventId: 'test-event-id',
    );

    $data = $event->toArray();

    expect($data['event_name'])->toBe('Purchase');
    expect($data['event_id'])->toBe('test-event-id');
    expect($data['action_source'])->toBe('website');
    expect($data)->toHaveKey('event_time');
});

it('includes custom data when provided', function () {
    $event = new ConversionEvent(
        eventName: 'Purchase',
        value: 99.99,
        currency: 'SAR',
        contentIds: ['100', '101'],
        contentType: 'product',
        quantity: 2,
    );

    $data = $event->toArray();

    expect($data['custom_data']['value'])->toBe(99.99);
    expect($data['custom_data']['currency'])->toBe('SAR');
    expect($data['custom_data']['content_ids'])->toBe(['100', '101']);
    expect($data['custom_data']['content_type'])->toBe('product');
    expect($data['custom_data']['num_items'])->toBe(2);
});

it('includes user data when provided', function () {
    $userData = new UserData;
    $userData->email('user@example.com');

    $event = new ConversionEvent(
        eventName: 'Purchase',
        userData: $userData,
    );

    $data = $event->toArray();

    expect($data['user_data']['em'])->toBe([hash('sha256', 'user@example.com')]);
});

it('filters out null values from array', function () {
    $event = new ConversionEvent(
        eventName: 'PageView',
    );

    $data = $event->toArray();

    expect($data)->not->toHaveKey('custom_data');
});

it('stores event id for deduplication', function () {
    $eventId = 'dedup-test-123';
    $event = new ConversionEvent(
        eventName: 'Purchase',
        eventId: $eventId,
    );

    expect($event->getEventId())->toBe($eventId);

    $data = $event->toArray();

    expect($data['event_id'])->toBe($eventId);
});

it('getters return correct values', function () {
    $userData = new UserData;
    $time = time();

    $event = new ConversionEvent(
        eventName: 'AddToCart',
        value: 50.00,
        currency: 'USD',
        contentIds: ['42'],
        contentType: 'product',
        contentCategory: 'electronics',
        contentName: 'iPhone',
        quantity: 1,
        searchString: null,
        status: null,
        eventSourceUrl: 'https://example.com',
        actionSource: 'website',
        userData: $userData,
        eventId: 'evt-42',
        eventTime: $time,
    );

    expect($event->getEventName())->toBe('AddToCart');
    expect($event->getValue())->toBe(50.00);
    expect($event->getCurrency())->toBe('USD');
    expect($event->getContentIds())->toBe(['42']);
    expect($event->getContentType())->toBe('product');
    expect($event->getContentCategory())->toBe('electronics');
    expect($event->getContentName())->toBe('iPhone');
    expect($event->getQuantity())->toBe(1);
    expect($event->getSearchString())->toBeNull();
    expect($event->getStatus())->toBeNull();
    expect($event->getEventSourceUrl())->toBe('https://example.com');
    expect($event->getActionSource())->toBe('website');
    expect($event->getUserData())->toBe($userData);
    expect($event->getEventId())->toBe('evt-42');
    expect($event->getEventTime())->toBe($time);
});
