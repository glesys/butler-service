<?php

namespace Tests\Database;

use Butler\Service\Database\HostParser;
use Butler\Service\Tests\TestCase;
use InvalidArgumentException;

class HostParserTest extends TestCase
{
    public function test_without_maintenance_returns_all_hosts()
    {
        $oneHost = ['1.1.1.1'];
        $twoHosts = ['1.1.1.1', '2.2.2.2'];
        $threeHosts = ['1.1.1.1', '2.2.2.2', '3.3.3.3'];

        $parser = new HostParser();

        $this->assertEquals($oneHost, $parser->parse($oneHost));
        $this->assertEquals($twoHosts, $parser->parse($twoHosts));
        $this->assertEquals($threeHosts, $parser->parse($threeHosts));
    }

    public function test_maintenance_with_one_host_returns_one_host()
    {
        $hosts = ['1.1.1.1'];

        $parser = (new HostParser())->maintenance(['* * * * *']);

        $this->assertEquals($hosts, $parser->parse($hosts));
    }

    public function test_maintenance_with_multiple_hosts()
    {
        $hosts = ['1.1.1.1', '2.2.2.2', '3.3.3.3', '4.4.4.4', '5.5.5.5'];

        $parser = (new HostParser())->maintenance([
            '* 1 * * *',
            '* 2 * * *',
            '* 1 * * *',
            '* 2 * * *',
            '* 3 * * *',
        ]);

        $this->travelTo('00:59');
        $this->assertEquals($hosts, $parser->parse($hosts));

        $this->travelTo('01:00');
        $this->assertEquals(['2.2.2.2', '4.4.4.4', '5.5.5.5'], $parser->parse($hosts));

        $this->travelTo('02:00');
        $this->assertEquals(['1.1.1.1', '3.3.3.3', '5.5.5.5'], $parser->parse($hosts));

        $this->travelTo('03:00');
        $this->assertEquals(['1.1.1.1', '2.2.2.2', '3.3.3.3', '4.4.4.4'], $parser->parse($hosts));

        $this->travelTo('05:00');
        $this->assertEquals($hosts, $parser->parse($hosts));
    }

    public function test_maintenance_can_not_remove_all_hosts()
    {
        $hosts = ['1.1.1.1', '2.2.2.2'];

        $parser = (new HostParser())->maintenance([
            '* * * * *',
            '* * * * *',
        ]);

        $this->assertEquals(['2.2.2.2'], $parser->parse($hosts));
    }

    public function test_maintenance_with_invalid_cron_expression_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid is not a valid CRON expression');

        (new HostParser())
            ->maintenance(['* * * * *', 'invalid', '* * * * *'])
            ->parse(['1.1.1.1', '2.2.2.2', '3.3.3.3']);
    }
}
