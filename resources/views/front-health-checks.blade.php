<div class="bg-white rounded p-4 mb-4">
  <div class="flex flex-row mb-2">
    <div class="flex-1 font-bold tracking-wide uppercase">Health Checks</div>
    <div class="rounded bg-gray-100 border px-2">
      <a class="text-blue-600" href="{{ route('health') }}">{{ route('health', [], false) }}</a>
      <span
        class="bg-secondary ml-1 px-1 text-xs text-white rounded cursor-default"
        title="Only GET method allowed"
      >GET</span>
      <span
        class="bg-purple-500 ml-1 px-1 text-xs text-white rounded cursor-default"
        title="Returns JSON"
      >JSON</span>
    </div>
  </div>

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
</div>
