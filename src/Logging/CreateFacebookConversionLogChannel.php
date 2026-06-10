<?php

declare(strict_types=1);

namespace Aghfatehi\LaravelMetaConversions\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class CreateFacebookConversionLogChannel
{
    public function __invoke(array $config): Logger
    {
        $handler = new RotatingFileHandler(
            storage_path('logs/facebook-conversion.log'),
            $config['days'] ?? 30,
            $config['level'] ?? Logger::DEBUG,
        );

        $handler->setFormatter(new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s',
            true,
            true,
        ));

        return new Logger('facebook_conversion', [$handler]);
    }
}
