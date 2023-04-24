<?php

declare(strict_types=1);

namespace Butler\Service\Tests\Jobs;

use Butler\Audit\Bus\WithCorrelation;
use Butler\Service\Jobs\QueueableJob;
use Butler\Service\Tests\TestCase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueableJobTest extends TestCase
{
    public function test_is_queueable()
    {
        $job = new class extends QueueableJob
        {
            //
        };

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_uses()
    {
        $uses = class_uses(QueueableJob::class);

        $this->assertContains(Dispatchable::class, $uses);
        $this->assertContains(InteractsWithQueue::class, $uses);
        $this->assertContains(Queueable::class, $uses);
        $this->assertContains(SerializesModels::class, $uses);
        $this->assertContains(WithCorrelation::class, $uses);
    }
}
