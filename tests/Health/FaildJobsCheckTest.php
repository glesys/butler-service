<?php

namespace Butler\Service\Tests\Health;

use Butler\Service\Health\Checks\FailedJobs;
use Butler\Service\Health\Result;
use Butler\Service\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FailedJobsCheckTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['queue.failed.database' => 'sqlite']);

        Schema::create(config('queue.failed.table'), fn($table) => $table->id());
    }

    public function test_ok_when_no_failed_jobs_exist()
    {
        $result = (new FailedJobs())->run();

        $this->assertEquals('No failed jobs.', $result->message);
        $this->assertEquals(Result::OK, $result->status);
        $this->assertEquals(0, $result->value());
    }

    public function test_critical_when_failed_jobs_exist()
    {
        DB::table(config('queue.failed.table'))->insert(['id' => 1]);

        $result = (new FailedJobs())->run();

        $this->assertEquals('1 failed jobs.', $result->message);
        $this->assertEquals(Result::CRITICAL, $result->status);
        $this->assertEquals(1, $result->value());
    }

    public function test_unknown_when_table_dont_exist()
    {
        config(['queue.failed.table' => 'foobar']);

        $result = (new FailedJobs())->run();

        $this->assertEquals('Table foobar not found.', $result->message);
        $this->assertEquals(Result::UNKNOWN, $result->status);
        $this->assertNull($result->value());
    }
}
