<?php

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\Exceptions\InvalidConfigException;
use Aghfatehi\LaravelMetaConversions\Services\MetaConversionApiClient;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;

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
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('json')->andReturn(['events_received' => 1]);
    $response->shouldReceive('failed')->andReturn(false);

    $pending = Mockery::mock(stdClass::class);
    $pending->shouldReceive('retry')->andReturn($pending);
    $pending->shouldReceive('asJson')->andReturn($pending);
    $pending->shouldReceive('post')->andReturn($response);

    $http = Mockery::mock(HttpClient::class);
    $http->shouldReceive('timeout')->andReturn($pending);

    $client = new MetaConversionApiClient($http);
    $event = new ConversionEvent(eventName: 'PageView', eventId: 'test-id');

    $result = $client->sendEvent($event);

    expect($result['events_received'])->toBe(1);
});

it('throws exception on api failure', function () {
    $response = Mockery::mock(Response::class);
    $response->shouldReceive('json')->andReturn([
        'error' => ['message' => 'Invalid token'],
    ]);
    $response->shouldReceive('failed')->andReturn(true);
    $response->shouldReceive('status')->andReturn(401);

    $pending = Mockery::mock(stdClass::class);
    $pending->shouldReceive('retry')->andReturn($pending);
    $pending->shouldReceive('asJson')->andReturn($pending);
    $pending->shouldReceive('post')->andReturn($response);

    $http = Mockery::mock(HttpClient::class);
    $http->shouldReceive('timeout')->andReturn($pending);

    $client = new MetaConversionApiClient($http);
    $event = new ConversionEvent(eventName: 'PageView', eventId: 'test-id');

    expect(fn () => $client->sendEvent($event))
        ->toThrow(\Aghfatehi\LaravelMetaConversions\Exceptions\FacebookConversionException::class);
});

it('includes test event code when configured', function () {
    config()->set('facebook-conversion-service.test_event_code', 'TEST123');

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('json')->andReturn(['events_received' => 1]);
    $response->shouldReceive('failed')->andReturn(false);

    $body = null;
    $pending = Mockery::mock(stdClass::class);
    $pending->shouldReceive('retry')->andReturn($pending);
    $pending->shouldReceive('asJson')->andReturn($pending);
    $pending->shouldReceive('post')->with(
        Mockery::any(),
        Mockery::capture($body),
    )->andReturn($response);

    $http = Mockery::mock(HttpClient::class);
    $http->shouldReceive('timeout')->andReturn($pending);

    $client = new MetaConversionApiClient($http);
    $event = new ConversionEvent(eventName: 'PageView', eventId: 'test-id');

    $client->sendEvent($event);

    expect($body['test_event_code'])->toBe('TEST123');
});

it('uses configured api version', function () {
    config()->set('facebook-conversion-service.api_version', 'v22.0');

    $response = Mockery::mock(Response::class);
    $response->shouldReceive('json')->andReturn(['events_received' => 1]);
    $response->shouldReceive('failed')->andReturn(false);

    $url = null;
    $pending = Mockery::mock(stdClass::class);
    $pending->shouldReceive('retry')->andReturn($pending);
    $pending->shouldReceive('asJson')->andReturn($pending);
    $pending->shouldReceive('post')->with(
        Mockery::capture($url),
        Mockery::any(),
    )->andReturn($response);

    $http = Mockery::mock(HttpClient::class);
    $http->shouldReceive('timeout')->andReturn($pending);

    $client = new MetaConversionApiClient($http);
    $event = new ConversionEvent(eventName: 'PageView', eventId: 'test-id');

    $client->sendEvent($event);

    expect($url)->toContain('v22.0');
});
