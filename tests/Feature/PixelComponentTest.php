<?php

it('renders pixel component with pixel id', function () {
    config()->set('facebook-conversion-service.pixel_id', 'TEST_PIXEL_123');
    config()->set('facebook-conversion-service.enabled', true);

    $html = view('facebook-conversion::pixel')->render();

    expect($html)->toContain('fbq("init", "TEST_PIXEL_123")');
    expect($html)->toContain('fbq("track", "PageView")');
});

it('does not render pixel when disabled', function () {
    config()->set('facebook-conversion-service.pixel_id', 'TEST_PIXEL_123');
    config()->set('facebook-conversion-service.enabled', false);

    $html = view('facebook-conversion::pixel')->render();

    expect($html)->not->toContain('fbq');
});

it('renders pixel view via include', function () {
    config()->set('facebook-conversion-service.pixel_id', 'TEST_PIXEL_123');
    config()->set('facebook-conversion-service.enabled', true);

    $view = view('facebook-conversion::pixel')->render();

    expect($view)->toContain('fbq("init", "TEST_PIXEL_123")');
    expect($view)->toContain('fbq("track", "PageView")');
});

it('includes event id in pixel view when provided', function () {
    config()->set('facebook-conversion-service.pixel_id', 'TEST_PIXEL_123');
    config()->set('facebook-conversion-service.enabled', true);

    $view = view('facebook-conversion::pixel', [
        'eventName' => 'Purchase',
        'eventId' => 'dedup-123',
        'customData' => ['value' => 99.99, 'currency' => 'SAR'],
    ])->render();

    expect($view)->toContain('dedup-123');
    expect($view)->toContain('Purchase');
    expect($view)->toContain('99.99');
});
