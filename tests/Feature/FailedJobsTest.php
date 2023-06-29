<?php

namespace Butler\Service\Tests\Feature;

use App\Jobs\Job;
use App\Jobs\ViewableJob;
use Butler\Service\Testing\Concerns\InteractsWithAuthentication;
use Butler\Service\Tests\TestCase;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Queue\Failed\FailedJobProviderInterface;
use Illuminate\Support\Facades\Artisan;
use Mockery\Mock;

class FailedJobsTest extends TestCase
{
    use InteractsWithAuthentication;

    /** @var FailedJobProviderInterface|Mock */
    private $queueFailer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queueFailer = $this->mock(FailedJobProviderInterface::class);
    }

    public function test_index_returns_correct_information()
    {
        $this->freezeTime();

        $this->queueFailer->expects('all')->andReturn([
            $this->makeFailedJob(['displayName' => 'App\\Jobs\\Job1'], '1'),
            $this->makeFailedJob(['displayName' => 'App\\Jobs\\Job2'], '2'),
        ]);

        $this->get(route('failed-jobs.index'))->assertOk()->assertExactJson([
            [
                'id' => '1',
                'failedAt' => now()->toDateTimeString(),
                'name' => 'Job1',
                'url' => 'http://localhost/failed-jobs/1',
            ],
            [
                'id' => '2',
                'failedAt' => now()->toDateTimeString(),
                'name' => 'Job2',
                'url' => 'http://localhost/failed-jobs/2',
            ],
        ]);
    }

    public function test_index_handles_job_with_invalid_json_payload()
    {
        $this->queueFailer->expects('all')->andReturn([
            $this->makeFailedJob('invalid-json'),
            $this->makeFailedJob(['displayName' => 'App\\Jobs\\Job2']),
        ]);

        $this->get(route('failed-jobs.index'))
            ->assertOk()
            ->assertJsonPath('*.name', ['n/a', 'Job2']);
    }

    public function test_index_handles_encrypted_job()
    {
        $this->queueFailer->expects('all')->andReturn([
            $this->makeFailedJob(['data' => ['command' => 'encrypted-command']]),
            $this->makeFailedJob(['displayName' => 'App\\Jobs\\Job2']),
        ]);

        $this->mock(Encrypter::class)
            ->expects('decrypt')
            ->with('encrypted-command')
            ->andReturn(serialize(new Job()));

        $this->withoutMiddleware(EncryptCookies::class)
            ->get(route('failed-jobs.index'))
            ->assertOk()
            ->assertJsonPath('*.name', ['Job', 'Job2']);
    }

    public function test_show_as_guest()
    {
        $this->get(route('failed-jobs.show', 1))->assertRedirectToRoute('home');
    }

    public function test_show_as_user()
    {
        $this->queueFailer->expects('find')->with(1)->andReturn(
            $this->makeFailedJob(['displayName' => 'App\\Jobs\\Job'])
        );

        $this->actingAsUser()
            ->withoutVite()
            ->get(route('failed-jobs.show', 1))
            ->assertOk()
            ->assertViewIs('butler::failed-job');
    }

    public function test_show_with_viewable_job_as_user()
    {
        $job = new ViewableJob();

        $this->queueFailer->expects('find')->with(1)->andReturn(
            $this->makeFailedJob(['data' => ['command' => serialize($job)]])
        );

        $this->actingAsUser()
            ->withoutVite()
            ->get(route('failed-jobs.show', 1))
            ->assertOk()
            ->assertViewIs('butler::failed-job')
            ->assertViewHas('meta', $job->viewData());
    }

    public function test_retry_as_guest()
    {
        $this->post(route('failed-jobs.retry'))->assertRedirectToRoute('home');
    }

    public function test_retry_as_user()
    {
        Artisan::command('queue:retry {id*}', fn ($id) => true);

        $this->actingAsUser()
            ->post(route('failed-jobs.retry'), ['ids' => [1, 2]])
            ->assertNoContent();
    }

    public function test_forget_as_guest()
    {
        $this->post(route('failed-jobs.forget'))->assertRedirectToRoute('home');
    }

    public function test_forget_as_user()
    {
        $this->queueFailer->expects()->forget(1);
        $this->queueFailer->expects()->forget(2);

        $this->actingAsUser()
            ->post(route('failed-jobs.forget'), ['ids' => [1, 2]])
            ->assertNoContent();
    }

    private function makeFailedJob(string|array $payload, $id = null): object
    {
        return (object) [
            'id' => $id ?? uniqid(),
            'connection' => 'connection',
            'queue' => 'queue',
            'exception' => 'exception',
            'failed_at' => now()->toDateTimeString(),
            'payload' => is_string($payload) ? $payload : json_encode($payload),
        ];
    }
}
