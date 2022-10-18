<?php

declare(strict_types=1);

namespace Butler\Service\Database;

use Cron\CronExpression;

class HostParser
{
    private array $maintenanceCronExpressions = [];

    public function maintenance(array $cronExpressions = []): static
    {
        $this->maintenanceCronExpressions = $cronExpressions;

        return $this;
    }

    public function parse(array $hosts): array
    {
        if (empty($this->maintenanceCronExpressions) || count($hosts) < 2) {
            return $hosts;
        }

        $now = now()->toDateTimeString();

        foreach ($this->maintenanceCronExpressions as $hostIndex => $cronExpression) {
            $cron = new CronExpression($cronExpression);

            if (isset($hosts[$hostIndex]) && count($hosts) > 1 && $cron->isDue($now)) {
                unset($hosts[$hostIndex]);
            }
        }

        return array_values($hosts);
    }
}
