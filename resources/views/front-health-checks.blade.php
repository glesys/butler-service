<x-butler-service::front-block title="Health checks" http-method="GET" response-type="JSON" :url="route('health', [], false)">
  @forelse($checks->groupBy('group')->sortKeys() as $group => $checks)
    <div class="font-bold my-3 text-sm tracking-wide uppercase">{{ Str::title($group) }}</div>
    @foreach($checks as $check)
      <div class="bg-check-{{ $check['result']->state }} m-1 px-3 py-2 text-sm text-white rounded">
        <abbr title="{{ $check['description'] }}">{{ $check['name'] }}</abbr>
        &ndash; {{ $check['result']->message }}
      </div>
    @endforeach
  @empty
    <div class="bg-secondary m-1 px-3 py-2 text-sm text-white rounded">
      No health checks found.
    </div>
  @endforelse
</x-butler-service::front-block>