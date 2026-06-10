<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Console\Commands;

use Aghfatehi\LaravelMetaConversions\Facades\FacebookConversion;
use Illuminate\Console\Command;

class TestFacebookConversionCommand extends Command
{
    protected $signature = 'facebook:test-event
        {event? : The event name to test (default: PageView)}
        {--value= : Optional event value}
        {--currency=SAR : Optional currency code}';

    protected $description = 'Send a test event to Facebook Conversion API';

    public function handle(): int
    {
        $eventName = $this->argument('event') ?? 'PageView';

        $this->info("Sending test event: {$eventName}");

        try {
            $builder = FacebookConversion::event()->name($eventName);

            if ($value = $this->option('value')) {
                $builder->value((float) $value);
            }

            $builder->currency($this->option('currency'));

            $result = $builder->send();

            $this->info('Event sent successfully!');
            $this->line(json_encode($result, JSON_PRETTY_PRINT));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to send event: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
