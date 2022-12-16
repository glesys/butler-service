<x-butler::layout>
  <x-butler::container>

    <x-butler::card title="Details" class="lg:w-1/2">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-24 gap-y-4">
        @foreach($about as $category => $values)
          <div class="mb-4">
            <x-butler::h2>{{ str($category)->headline() }}</x-butler::h2>
            @foreach($values as $key => $value)
              <div class="flex justify-between">
                <x-butler::label>{{ str($key)->headline() }}</x-butler::label>
                @if($value === 'NOT CACHED')
                  <span class="text-red-dark">{{ $value }}</span>
                @elseif($value === 'CACHED')
                  <span class="text-green-dark">{{ $value }}</span>
                @elseif(is_bool($value))
                  <x-butler::muted>{{ $value ? 'TRUE' : 'FALSE' }}</x-butler::muted>
                @else
                  <x-butler::muted>{{ $value }}</x-butler::muted>
                @endif
              </div>
            @endforeach
          </div>
        @endforeach
      </div>
    </x-butler::card>

    <x-butler::card title="Database connections" class="lg:w-1/2">
      <div class="flex flex-col gap-4">
        @foreach($databaseConnections as $key => $connection)
          <x-butler::accordion>
            <x-butler::badge ::class="badgeClassName('{{ $connection['connected'] ? 'ok' : 'critical' }}')"/>
            <span class="ml-4 text-lg">
              <x-butler::label>{{ $key }}</x-butler::label>
              @if($connection['host'])
                <x-butler::muted>&dash; {{ $connection['host'] }}</x-butler::muted>
              @endif
            </span>
            <x-slot name="content">
              <div class="flex justify-between">
                <x-butler::label>driver</x-butler::label>
                <x-butler::muted>{{ $connection['driver'] }}</x-butler::muted>
              </div>
              <div class="flex justify-between">
                <x-butler::label>port</x-butler::label>
                <x-butler::muted>{{ $connection['port'] }}</x-butler::muted>
              </div>
              <div class="flex justify-between">
                <x-butler::label>charset</x-butler::label>
                <x-butler::muted>{{ $connection['charset'] }}</x-butler::muted>
              </div>
              <div class="flex justify-between">
                <x-butler::label>collation</x-butler::label>
                <x-butler::muted>{{ $connection['collation'] }}</x-butler::muted>
              </div>
            </x-slot>
          </x-butler::accordion>
        @endforeach
      </div>
    </x-butler::card>

  </x-butler::container>
</x-butler::layout>
