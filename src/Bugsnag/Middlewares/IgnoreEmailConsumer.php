<?php

declare(strict_types=1);

namespace Butler\Service\Bugsnag\Middlewares;

use Bugsnag\Report;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class IgnoreEmailConsumer
{
    public function __invoke(Report $report)
    {
        try {
            $user = $report->getUser();

            if ($user instanceof Authenticatable) {
                return false;
            }

            if (filter_var($user['name'] ?? null, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        } catch (Exception) {
            //
        }
    }
}
