<?php

declare(strict_types=1);

namespace Butler\Service\Logging;

use Butler\Audit\Facades\Auditor;
use Gelf\Publisher;
use Gelf\Transport\IgnoreErrorTransportWrapper;
use Gelf\Transport\UdpTransport;
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
            app()->makeWith(UdpTransport::class, ['host' => $config['host'], 'port' => $config['port']])
        );

        $handler = new GelfHandler(new Publisher($transport));

        $handler
            ->setFormatter(new GelfMessageFormatter())
            ->pushProcessor(function ($record) use ($config) {
                $record['extra'][$config['name_key']] = $config['name'];
                $record['extra']['trace_id'] = Auditor::correlationId();

                return $record;
            });

        return new Logger('', [$handler]);
    }
}
