<?php

namespace Butler\Service\Http\Controllers;

use Butler\Service\Foundation\Application;
use Butler\Service\Health\Check;
use Butler\Service\Health\Checks;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HealthController extends Controller
{
    public function __invoke()
    {
        $data = [
            'service' => $this->serviceInfo(),
            'checks' => $this->checks(),
        ];

        if (request()->wantsJson()) {
            return $data;
        }

        return view('service::health', $data);
    }

    private function serviceInfo(): array
    {
        return [
            'php' => PHP_VERSION,
            'laravel' => Application::VERSION,
            'name' => config('app.name'),
            'timezone' => config('app.timezone'),
        ];
    }

    private function checks(): Collection
    {
        return collect($this->coreChecks())
            ->merge(config('butler.service.health.checks', []))
            ->map(fn($class) => $this->checkToArray(app($class)))
            ->sortByDesc(fn($check) => $check['result']->order());
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
