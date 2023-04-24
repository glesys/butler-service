<x-butler::layout>
  <x-butler::container>

    <x-butler::card title="Details" class="lg:w-1/2">
      <div
        x-cloak
        x-data="{ about: {} }"
        x-init="$watch('$store.health.about', data => about = data)"
        class="grid grid-cols-1 lg:grid-cols-2 gap-x-24 gap-y-4"
      >
        <template x-for="(values, category) in about">
          <div class="mb-4">
            <x-butler::h2 class="capitalize" x-text="deslug(category)"></x-butler::h2>
            <template x-for="(value, key) in values">
              <div class="flex justify-between">
                <x-butler::label class="capitalize" x-text="deslug(key)"></x-butler::label>

                <span x-show="category === 'cache'" :class="value ? 'text-green-dark' : 'text-red-dark'" x-text="value"></span>
                <x-butler::muted x-show="category !== 'cache'" x-text="value"></x-butler::muted>
              </div>
            </template>
          </div>
        </template>
      </div>
    </x-butler::card>

    <x-butler::card title="Database connections" class="lg:w-1/2">
      <div class="flex flex-col gap-4">
        @foreach($databaseConnections as $key => $connection)
          <x-butler::accordion>
            <x-butler::badge ::class="badgeClassName('{{ $connection['connected'] ? 'ok' : 'critical' }}')"/>
            <span class="ml-4 text-lg">
              <x-butler::label>{{ $key }}</x-butler::label>
              @if($connection['host'] && is_string($connection['host']))
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
