<?php

namespace Butler\Service\Repositories;

use Butler\Service\Foundation\Application;
use Butler\Service\Health\Check;
use Butler\Service\Health\Checks;
use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HealthRepository
{
    public function __invoke()
    {
        return [
            'service' => $this->serviceInfo(),
            'checks' => $this->checks(),
        ];
    }

    private function serviceInfo(): array
    {
        return [
            'php' => PHP_VERSION,
            'laravel' => Application::VERSION,
            'butlerService' => ltrim(InstalledVersions::getPrettyVersion('glesys/butler-service'), 'v'),
            'name' => config('app.name'),
            'timezone' => config('app.timezone'),
        ];
    }

    private function checks(): Collection
    {
        return collect($this->coreChecks())
            ->merge(config('butler.service.health.checks', []))
            ->map(fn($class) => $this->checkToArray(app($class)))
            ->sortByDesc(fn($check) => $check['result']->order())
            ->values();
    }

    private function coreChecks(): array
    {
        if (config('butler.service.health.core', true)) {
            return [
                Checks\Database::class,
                Checks\Redis::class,
                Checks\FailedJobs::class,
            ];
        }

        return [];
    }

    private function checkToArray(Check $check): array
    {
        $name = Str::of($check->name ?? class_basename($check))
            ->kebab()
            ->replace('-', ' ')
            ->title();

        return [
            'name' => (string) $name,
            'slug' => $check->slug ?? (string) $name->slug(),
            'group' => $check->group ?? 'other',
            'description' => $check->description,
            'result' => $check->run(),
        ];
    }
}
