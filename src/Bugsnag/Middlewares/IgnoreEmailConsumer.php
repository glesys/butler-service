<?php

namespace Butler\Service\Bugsnag\Middlewares;

use Bugsnag\Report;
use Exception;

class IgnoreEmailConsumer
{
    public function __invoke(Report $report)
    {
        try {
            if (filter_var($report->getUser()['name'] ?? null, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
        } catch (Exception) {
            //
        }
    }
}
