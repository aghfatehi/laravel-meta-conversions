<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Jobs;

use Aghfatehi\LaravelMetaConversions\DTOs\ConversionEvent;
use Aghfatehi\LaravelMetaConversions\Services\MetaConversionApiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFacebookEventJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 30;

    public int $tries = 3;

    public function __construct(
        private readonly ConversionEvent $event,
    ) {}

    public function handle(MetaConversionApiClient $client): void
    {
        $client->sendEvent($this->event);
    }

    public function failed(\Throwable $e): void
    {
        logger()->error('Facebook Conversion API job failed', [
            'event_name' => $this->event->getEventName(),
            'event_id' => $this->event->getEventId(),
            'error' => $e->getMessage(),
        ]);
    }
}
