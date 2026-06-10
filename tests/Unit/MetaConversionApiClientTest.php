<?php

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\Exceptions\InvalidConfigException;
use Aghfatehi\LaravelMetaConversions\Services\MetaConversionApiClient;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use GuzzleHttp\Psr7\Response as Psr7Response;

beforeEach(function () {
    config()->set('facebook-conversion-service.pixel_id', '123456');
    config()->set('facebook-conversion-service.access_token', 'test-token');
    config()->set('facebook-conversion-service.logging_enabled', false);
});

it('validates configuration and throws on missing pixel id', function () {
    config()->set('facebook-conversion-service.pixel_id', '');

    $client = app(MetaConversionApiClient::class);

    expect(fn () => $client->validateConfig())->toThrow(InvalidConfigException::class, 'Pixel ID');
});

it('validates configuration and throws on missing access token', function () {
    config()->set('facebook-conversion-service.access_token', '');

    $client = app(MetaConversionApiClient::class);

    expect(fn () => $client->validateConfig())->toThrow(InvalidConfigException::class, 'token');
});

it('sends event via http client', function () {
    $http = app(HttpClient::class);
    $client = new MetaConversionApiClient($http);

    $event = new ConversionEvent(
        eventName: 'PageView',
        eventId: 'test-id',
    );

    Http::fake([
        'graph.facebook.com/*' => Http::response(['events_received' => 1], 200),
    ]);

    $result = $client->sendEvent($event);

    expect($result['events_received'])->toBe(1);
});

it('throws exception on api failure', function () {
    $http = app(HttpClient::class);
    $client = new MetaConversionApiClient($http);

    $event = new ConversionEvent(
        eventName: 'PageView',
        eventId: 'test-id',
    );

    Http::fake([
        'graph.facebook.com/*' => Http::response([
            'error' => ['message' => 'Invalid token'],
        ], 401),
    ]);

    expect(fn () => $client->sendEvent($event))
        ->toThrow(\Aghfatehi\LaravelMetaConversions\Exceptions\FacebookConversionException::class);
});

it('includes test event code when configured', function () {
    config()->set('facebook-conversion-service.test_event_code', 'TEST123');

    $http = app(HttpClient::class);
    $client = new MetaConversionApiClient($http);

    $event = new ConversionEvent(
        eventName: 'PageView',
        eventId: 'test-id',
    );

    Http::fake(function ($request) {
        $body = $request->data();

        expect($body['test_event_code'])->toBe('TEST123');

        return Http::response(['events_received' => 1], 200);
    });

    $client->sendEvent($event);
});

it('uses configured api version', function () {
    config()->set('facebook-conversion-service.api_version', 'v22.0');

    $http = app(HttpClient::class);
    $client = new MetaConversionApiClient($http);

    $event = new ConversionEvent(
        eventName: 'PageView',
        eventId: 'test-id',
    );

    Http::fake(function ($request) {
        expect($request->url())->toContain('v22.0');

        return Http::response(['events_received' => 1], 200);
    });

    $client->sendEvent($event);
});
