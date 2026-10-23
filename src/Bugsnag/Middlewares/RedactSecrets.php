<?php

declare(strict_types=1);

namespace Butler\Service\Bugsnag\Middlewares;

use Bugsnag\Report;
use Closure;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RedactSecrets
{
    /**
     * Values shorter than this are not redacted, to avoid mangling messages
     * with replacements of common short strings.
     */
    public const MIN_LENGTH = 8;

    public const DEFAULT_CONFIG = [
        'app.key',
        'app.previous_keys.*',
        'bugsnag.api_key',
        'services.*.webhook',
        'services.*.webhooks.*',
        '*.password',
        '*.secret',
        '*_secret',
        '*.token',
        '*_token',
        '*api_key',
    ];

    public function __invoke(Report $report)
    {
        try {
            $secrets = $this->secrets(config('butler.service.redact_bugsnag_config', static::DEFAULT_CONFIG));

            $current = $report;

            while ($current) {
                if (is_string($message = $current->getMessage())) {
                    $current->setMessage($this->redact($message, $secrets));
                }

                $current = $this->previous($current);
            }

            $metaData = $report->getMetaData();

            array_walk_recursive($metaData, function (&$value) use ($secrets) {
                if (is_string($value)) {
                    $value = $this->redact($value, $secrets);
                }
            });

            $report->setMetaData($metaData, false);
        } catch (Exception) {
            //
        }
    }

    /**
     * @return array<string, string> secret value => replacement
     */
    protected function secrets(array $patterns): array
    {
        if (empty($patterns)) {
            return [];
        }

        $secrets = [];

        foreach (Arr::dot(config()->all()) as $key => $value) {
            if (! is_string($value) || strlen($value) < static::MIN_LENGTH) {
                continue;
            }

            if (Str::is($patterns, (string) $key)) {
                $secrets[$value] = $this->replacement($value, (string) $key);
            }
        }

        return $secrets;
    }

    protected function replacement(string $value, string $key): string
    {
        $parts = parse_url($value);

        if (isset($parts['scheme'], $parts['host'])) {
            return "{$parts['scheme']}://{$parts['host']}/[{$key}]";
        }

        return "[{$key}]";
    }

    protected function redact(string $text, array $secrets): string
    {
        $text = strtr($text, $secrets);

        return preg_replace([
            '#(?<=://)[^/\s:@\[]+:[^/\s@]+@#',
            '#(?<=https://hooks\.slack\.com/)(?!\[)[^\s"\'<>]+#',
        ], [
            '[redacted]@',
            '[redacted]',
        ], $text);
    }

    protected function previous(Report $report): ?Report
    {
        return Closure::bind(fn () => $this->previous, $report, Report::class)();
    }
}
