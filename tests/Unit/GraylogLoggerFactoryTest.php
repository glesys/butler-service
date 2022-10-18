<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Unit;

use Butler\Audit\Facades\Auditor;
use Butler\Service\Logging\GraylogLoggerFactory;
use Butler\Service\Tests\TestCase;
use Gelf\Message;
use Gelf\Transport\UdpTransport;
use Mockery;

class GraylogLoggerFactoryTest extends TestCase
{
    public function test_correctly_uses_configuration_and_correlation_id_from_container()
    {
        Auditor::correlationId('example-correlation-id');

        app()->bind(UdpTransport::class, function () {
            $transport = Mockery::mock(UdpTransport::class);
            $transport->expects()->send(
                Mockery::on(function (Message $message) {
                    return $message->getShortMessage() === 'Testing'
                        && $message->getAdditional('trace_id') === 'example-correlation-id'
                        && $message->getAdditional('service') === 'butler-service';
                })
            );

            return $transport;
        });

        $config = [
            'name' => 'butler-service',
            'name_key' => 'service',
            'host' => '127.0.0.1',
            'port' => 12201,
        ];

        $factory = new GraylogLoggerFactory($config);
        $logger = $factory($config);
        $logger->info('Testing');
    }
}
