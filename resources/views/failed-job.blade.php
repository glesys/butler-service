<x-butler::layout>
  <x-butler::container>
    <div class="w-full lg:w-1/3">
      <x-butler::card title="Failed Job Details" x-data="failedJobCard(
          '{{ $failedJob->id }}',
          '{{ route('failed-jobs.retry') }}',
          '{{ route('failed-jobs.forget') }}',
        )">
        <x-slot name="buttons">
          <x-butler::confirm-button dispatch="retry-confirmed" text="Retry" icon="retry" color="yellow"/>
          <x-butler::confirm-button dispatch="forget-confirmed" text="Forget"/>
        </x-slot>

        <div class="flex justify-between">
          <x-butler::label>ID</x-butler::label>
          <x-butler::muted class="select-all">{{ $failedJob->id }}</x-butler::muted>
        </div>
        <div class="flex justify-between">
          <x-butler::label>Name</x-butler::label>
          <x-butler::muted>{{ $name }}</x-butler::muted>
        </div>
        <div class="flex justify-between">
          <x-butler::label>Failed at</x-butler::label>
          <x-butler::muted>{{ $failedJob->failed_at }}</x-butler::muted>
        </div>
        <div class="flex justify-between">
          <x-butler::label>Connection</x-butler::label>
          <x-butler::muted>{{ $failedJob->connection }}</x-butler::muted>
        </div>
        <div class="flex justify-between">
          <x-butler::label>Queue</x-butler::label>
          <x-butler::muted>{{ $failedJob->queue }}</x-butler::muted>
        </div>
      </x-butler::card>

      @if($meta)
        <x-butler::card title="Meta">
          <pre><code class="language-json">{{ json_encode($meta, JSON_PRETTY_PRINT) }}</code></pre>
        </x-butler::card>
      @endif

      <x-butler::card title="Payload">
        <pre><code class="language-json">{{ json_encode($payload, JSON_PRETTY_PRINT) }}</code></pre>
      </x-butler::card>
    </div>

    <div class="lg:w-2/3">
      <x-butler::card title="Exception">
        <pre class="text-sm overflow-x-auto"><code class="nohighlight">{{ $failedJob->exception }}</code></pre>
      </x-butler::card>
    </div>
  </x-butler::container>
</x-butler::layout>
