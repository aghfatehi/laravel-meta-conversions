<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions;

use Aghfatehi\LaravelMetaConversions\Console\Commands\TestFacebookConversionCommand;
use Aghfatehi\LaravelMetaConversions\Contracts\FacebookConversionService as FacebookConversionServiceContract;
use Aghfatehi\LaravelMetaConversions\Http\Middleware\TrackFacebookPageView;
use Aghfatehi\LaravelMetaConversions\Logging\CreateFacebookConversionLogChannel;
use Aghfatehi\LaravelMetaConversions\Services\FacebookConversionService;
use Aghfatehi\LaravelMetaConversions\Services\MetaConversionApiClient;
use Aghfatehi\LaravelMetaConversions\View\Components\Pixel;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class FacebookConversionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/facebook-conversion-service.php',
            'facebook-conversion-service'
        );

        $this->registerBindings();

        $this->registerLogChannel();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/facebook-conversion-service.php' => config_path('facebook-conversion-service.php'),
            ], 'facebook-conversion-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/facebook-conversion'),
            ], 'facebook-conversion-views');

            $this->commands([
                TestFacebookConversionCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'facebook-conversion');

        $this->loadViewComponentsAs('facebook-conversion', [
            Pixel::class,
        ]);

        $this->registerMiddleware();
    }

    private function registerBindings(): void
    {
        $this->app->singleton(MetaConversionApiClient::class, function ($app) {
            return new MetaConversionApiClient($app[HttpClient::class]);
        });

        $this->app->singleton(FacebookConversionServiceContract::class, function ($app) {
            return new FacebookConversionService($app[MetaConversionApiClient::class]);
        });

        $this->app->alias(FacebookConversionServiceContract::class, 'facebook-conversion-service');
    }

    private function registerLogChannel(): void
    {
        $channel = config('facebook-conversion-service.log_channel', 'facebook_conversion');

        if (config('facebook-conversion-service.logging_enabled', true)) {
            Log::extend($channel, function ($app, $config) {
                return (new CreateFacebookConversionLogChannel)($config);
            });
        }
    }

    private function registerMiddleware(): void
    {
        if (method_exists($this->app['router'], 'aliasMiddleware')) {
            $this->app['router']->aliasMiddleware('track-facebook-pageview', TrackFacebookPageView::class);
        }
    }
}
