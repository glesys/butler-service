<?php

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
        $job = new class extends QueueableJob {
            //
        };

        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    public function test_uses()
    {
        $uses = class_uses(QueueableJob::class);

        $this->assertInArray(Dispatchable::class, $uses);
        $this->assertInArray(InteractsWithQueue::class, $uses);
        $this->assertInArray(Queueable::class, $uses);
        $this->assertInArray(SerializesModels::class, $uses);
        $this->assertInArray(WithCorrelation::class, $uses);
    }
}
