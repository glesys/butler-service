<?php

namespace Butler\Service\Tests\Bus;

use Butler\Service\Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Butler\Audit\Facades\Auditor;

class DispatcherTest extends TestCase
{
    public function test_it_sets_correlation_id_for_job_using_WithCorrelationId_trait()
    {
        Auditor::fake();
        Queue::fake();

        Auditor::correlationId('a-correlation-id');

        dispatch(new JobWithCorrelationId());

        Queue::assertPushed(function (JobWithCorrelationId $job) {
            return $job->correlationId === 'a-correlation-id';
        });
    }

    public function test_it_does_not_set_correlation_id_only_for_job_not_using_WithCorrelationId_trait()
    {
        Auditor::fake();
        Queue::fake();

        dispatch(new JobWithoutCorrelationId());

        Queue::assertPushed(function (JobWithoutCorrelationId $job) {
            return ! property_exists($job, 'correlationId');
        });
    }
}
