<?php

use Aghfatehi\LaravelMetaConversions\Http\Middleware\TrackFacebookPageView;

it('passes request through middleware', function () {
    config()->set('facebook-conversion-service.enabled', false);

    $request = Request::create('/', 'GET');
    $middleware = new TrackFacebookPageView;

    $response = $middleware->handle($request, function ($req) {
        return response('OK');
    });

    expect($response->getContent())->toBe('OK');
});
