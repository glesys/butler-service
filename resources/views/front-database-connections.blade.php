<x-butler-service::front-block title="Database connections">
  @forelse($databaseConnections as $name => $connection)
    <div class="font-bold my-3 text-sm tracking-wide uppercase">{{ str($name)->title() }}</div>
    @foreach($connection['hosts'] ?? [] as $host)
      @php
        $bgClass = $host['available'] ? 'bg-check-ok' : 'bg-check-unknown';
      @endphp

      <div class="flex justify-between m-1 px-3 py-2 text-sm text-white rounded {{ $bgClass }}">
        <span>{{ $host['address'] }}</span>
        @if($host['maintenance'])
          <abbr title="maintenance cron expression">{{ $host['maintenance'] }}</abbr>
        @endif
      </div>
    @endforeach
  @empty
    <div class="bg-secondary m-1 px-3 py-2 text-sm text-white rounded">
      No database connections.
    </div>
  @endforelse
</x-butler-service::front-block>
