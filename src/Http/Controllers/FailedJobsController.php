<?php

namespace Butler\Service\Http\Controllers;

use Butler\Service\Jobs\Contracts\Viewable;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Queue\Failed\FailedJobProviderInterface;
use Illuminate\Support\Facades\Artisan;

class FailedJobsController extends Controller
{
    public function __construct(protected FailedJobProviderInterface $queueFailer)
    {
        $this->middleware(Authenticate::using('web'))->except('index');
    }

    public function index()
    {
        return collect($this->queueFailer->all())->map(function ($failedJob) {
            $details = $this->getDetailsFromRawPayload($failedJob->payload);

            return [
                'id' => $failedJob->id,
                'failedAt' => $failedJob->failed_at,
                'name' => $details['name'],
                'url' => route('failed-jobs.show', $failedJob->id),
            ];
        });
    }

    public function show($id)
    {
        $failedJob = $this->queueFailer->find($id);

        $details = $this->getDetailsFromRawPayload($failedJob->payload);

        return view('butler::failed-job', $details + [
            'failedJob' => $failedJob,
        ]);
    }

    public function retry()
    {
        $input = $this->validate(request(), ['ids' => 'required|array']);

        Artisan::call('queue:retry', ['id' => $input['ids']]);

        return response()->noContent();
    }

    public function forget()
    {
        $input = $this->validate(request(), ['ids' => 'required|array']);

        foreach ($input['ids'] as $id) {
            $this->queueFailer->forget($id);
        }

        return response()->noContent();
    }

    protected function getDetailsFromRawPayload(string $payload): array
    {
        $payload = rescue(fn () => json_decode($payload, true, flags: JSON_THROW_ON_ERROR));

        $command = $this->getCommandFromPayloadData($payload['data'] ?? []);

        return [
            'payload' => $payload,
            'name' => $command
                ? class_basename($command)
                : str($payload['displayName'] ?? 'n/a')->afterLast('\\'),
            'meta' => $command instanceof Viewable
                ? $command->viewData()
                : null,
        ];
    }

    protected function getCommandFromPayloadData(array $data): ?object
    {
        return rescue(function () use ($data) {
            if (str_starts_with($data['command'], 'O:')) {
                return unserialize($data['command']);
            }

            if (app()->bound(Encrypter::class)) {
                return unserialize(app(Encrypter::class)->decrypt($data['command']));
            }
        }, report: false);
    }
}
