<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Bugsnag\Middlewares;

use Bugsnag\Configuration;
use Bugsnag\Report;
use Butler\Service\Bugsnag\Middlewares\RedactSecrets;
use Butler\Service\Tests\TestCase;
use Exception;

class RedactSecretsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'butler.service.redact_bugsnag_config' => ['services.*.webhook', '*.password'],
            'services.slack.webhook' => 'https://hooks.slack.com/services/T000/B000/secret',
            'database.connections.mysql.password' => 'database-password',
            'services.foo.url' => 'https://foo.localhost/not-secret',
        ]);
    }

    private function report(Exception $exception): Report
    {
        return Report::fromPHPThrowable(new Configuration('api-key'), $exception);
    }

    public function test_configured_values_are_redacted_from_message()
    {
        $report = $this->report(new Exception(
            'Timeout for https://hooks.slack.com/services/T000/B000/secret using database-password'
        ));

        (new RedactSecrets())($report);

        $this->assertSame(
            'Timeout for https://hooks.slack.com/[services.slack.webhook] using [database.connections.mysql.password]',
            $report->getMessage(),
        );
    }

    public function test_configured_values_are_redacted_from_previous_errors()
    {
        $report = $this->report(new Exception('outer', 0, new Exception(
            'inner https://hooks.slack.com/services/T000/B000/secret'
        )));

        (new RedactSecrets())($report);

        $this->assertSame(
            ['outer', 'inner https://hooks.slack.com/[services.slack.webhook]'],
            array_column($report->getErrors(), 'errorMessage'),
        );
    }

    public function test_configured_values_are_redacted_from_metadata()
    {
        $report = $this->report(new Exception('error'));
        $report->setMetaData(['tab' => ['nested' => ['value' => 'is database-password', 'number' => 1]]]);

        (new RedactSecrets())($report);

        $this->assertSame(
            ['tab' => ['nested' => ['value' => 'is [database.connections.mysql.password]', 'number' => 1]]],
            $report->getMetaData(),
        );
    }

    public function test_unconfigured_and_short_values_are_not_redacted()
    {
        config(['services.bar.webhook' => 'short']);

        $report = $this->report(new Exception('https://foo.localhost/not-secret short'));

        (new RedactSecrets())($report);

        $this->assertSame('https://foo.localhost/not-secret short', $report->getMessage());
    }

    public function test_url_credentials_are_redacted()
    {
        $report = $this->report(new Exception('Failed for mysql://user:pa55word@db.localhost/app'));

        (new RedactSecrets())($report);

        $this->assertSame('Failed for mysql://[redacted]@db.localhost/app', $report->getMessage());
    }

    public function test_unknown_slack_urls_are_redacted()
    {
        $report = $this->report(new Exception('Failed for https://hooks.slack.com/actions/T000/123/abc.'));

        (new RedactSecrets())($report);

        $this->assertSame('Failed for https://hooks.slack.com/[redacted]', $report->getMessage());
    }

    public function test_patterns_are_applied_when_nothing_is_configured()
    {
        config(['butler.service.redact_bugsnag_config' => []]);

        $report = $this->report(new Exception('database-password https://hooks.slack.com/services/T000/B000/secret'));

        (new RedactSecrets())($report);

        $this->assertSame('database-password https://hooks.slack.com/[redacted]', $report->getMessage());
    }

    public function test_default_config_is_used_when_not_configured()
    {
        config(['butler.service' => []]);

        $report = $this->report(new Exception('Timeout for https://hooks.slack.com/services/T000/B000/secret'));

        (new RedactSecrets())($report);

        $this->assertSame('Timeout for https://hooks.slack.com/[services.slack.webhook]', $report->getMessage());
    }
}
