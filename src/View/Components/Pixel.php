<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\View\Components;

use Illuminate\View\Component;

class Pixel extends Component
{
    public function __construct(
        public ?string $eventName = null,
        public ?string $eventId = null,
        public array $customData = [],
    ) {}

    public function render(): string
    {
        return 'facebook-conversion::pixel';
    }
}
