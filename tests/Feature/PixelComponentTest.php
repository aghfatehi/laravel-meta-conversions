<?php

use Aghfatehi\LaravelMetaConversions\View\Components\Pixel;

it('renders pixel component with pixel id', function () {
    config()->set('facebook-conversion-service.pixel_id', 'TEST_PIXEL_123');
    config()->set('facebook-conversion-service.enabled', true);

    $component = new Pixel;
    $html = view($component->render(), $component->data())->render();

    expect($html)->toContain('fbq("init", "TEST_PIXEL_123")');
    expect($html)->toContain('fbq("track", "PageView")');
});

it('does not render pixel when disabled', function () {
    config()->set('facebook-conversion-service.pixel_id', 'TEST_PIXEL_123');
    config()->set('facebook-conversion-service.enabled', false);

    $component = new Pixel;
    $html = view($component->render(), $component->data())->render();

    expect($html)->not->toContain('fbq');
});

it('includes event id in pixel view when provided', function () {
    config()->set('facebook-conversion-service.pixel_id', 'TEST_PIXEL_123');
    config()->set('facebook-conversion-service.enabled', true);

    $component = new Pixel('Purchase', 'dedup-123', ['value' => 99.99, 'currency' => 'SAR']);
    $html = view($component->render(), $component->data())->render();

    expect($html)->toContain('dedup-123');
    expect($html)->toContain('Purchase');
    expect($html)->toContain('99.99');
});
