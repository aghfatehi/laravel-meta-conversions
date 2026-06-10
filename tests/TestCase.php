<?php

namespace Aghfatehi\LaravelMetaConversions\Tests;

use Aghfatehi\LaravelMetaConversions\FacebookConversionServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FacebookConversionServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('facebook-conversion-service.pixel_id', 'TEST_PIXEL_ID');
        config()->set('facebook-conversion-service.access_token', 'TEST_ACCESS_TOKEN');
        config()->set('facebook-conversion-service.enabled', true);
        config()->set('facebook-conversion-service.logging_enabled', false);
    }
}
