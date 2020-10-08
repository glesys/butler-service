<?php

namespace Butler\Service\Testing\Concerns;

use DateTimeInterface;
use Illuminate\Support\Carbon;

trait InteractsWithTime
{
    public function travelTo($date, $callback = null)
    {
        $date = $date instanceof DateTimeInterface
            ? $date
            : Carbon::parse($date);

        return parent::travelTo($date, $callback);
    }
}
