<?php

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\Jobs\SendFacebookEventJob;

beforeEach(function () {
    config()->set('facebook-conversion-service.pixel_id', '123456');
    config()->set('facebook-conversion-service.access_token', 'test-token');
    config()->set('facebook-conversion-service.logging_enabled', false);

    Http::fake([
        'graph.facebook.com/*' => Http::response(['events_received' => 1], 200),
    ]);
});

it('dispatches job and sends event', function () {
    $event = new ConversionEvent(
        eventName: 'Purchase',
        value: 99.99,
        currency: 'SAR',
        contentIds: ['100'],
        eventId: 'job-test-123',
    );

    $job = new SendFacebookEventJob($event);

    $job->handle(app(\Aghfatehi\LaravelMetaConversions\Services\MetaConversionApiClient::class));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return $body['data'][0]['event_name'] === 'Purchase'
            && $body['data'][0]['event_id'] === 'job-test-123';
    });
});

it('has correct job configuration', function () {
    $event = new ConversionEvent(eventName: 'PageView');

    $job = new SendFacebookEventJob($event);

    expect($job->timeout)->toBe(30);
    expect($job->tries)->toBe(3);
});
