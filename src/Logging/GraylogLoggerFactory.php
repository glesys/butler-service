<?php

namespace Butler\Service\Logging;

use Gelf\Publisher;
use Gelf\Transport\IgnoreErrorTransportWrapper;
use Gelf\Transport\UdpTransport;
use Illuminate\Support\Str;
use Monolog\Formatter\GelfMessageFormatter;
use Monolog\Handler\GelfHandler;
use Monolog\Logger;

/**
 * @codeCoverageIgnore
 */
class GraylogLoggerFactory
{
    public function __invoke(array $config): Logger
    {
        $transport = new IgnoreErrorTransportWrapper(
            new UdpTransport($config['host'], $config['port'])
        );

        $handler = new GelfHandler(new Publisher($transport));

        $handler
            ->setFormatter(new GelfMessageFormatter())
            ->pushProcessor(function ($record) use ($config) {
                $record['extra'][$config['name_key']] = $config['name'];
                $record['extra']['trace_id'] = (string) Str::uuid(12);
                return $record;
            });

        return new Logger('', [$handler]);
    }
}
