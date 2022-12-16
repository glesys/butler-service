<x-butler::card title="Checks" x-data="healthChecksCard">
  <div x-cloak x-show="! sortedChecks.length">No health checks found.</div>
  <div x-cloak x-show="sortedChecks.length" class="flex flex-col gap-4">
    <template x-for="check in sortedChecks" :key="check.slug + check.timestamp">
      <x-butler::accordion>
        <x-butler::badge ::class="badgeClassName(check.result.state)"/>
        <span class="ml-4 text-lg">
          <x-butler::label x-text="check.name"></x-butler::label>
          <x-butler::muted x-text="'&ndash; ' + check.result.message"></x-butler::muted>
        </span>
        <x-slot name="content">
          <div class="flex justify-between">
            <x-butler::label>description</x-butler::label>
            <x-butler::muted x-text="check.description"></x-butler::muted>
          </div>
          <div class="flex justify-between">
            <x-butler::label>group</x-butler::label>
            <x-butler::muted x-text="check.group"></x-butler::muted>
          </div>
          <div class="flex justify-between">
            <x-butler::label>Runtime (ms)</x-butler::label>
            <x-butler::muted x-text="check.runtimeInMilliseconds"></x-butler::muted>
          </div>
        </x-slot>
      </x-butler::accordion>
    </template>
  </div>
</x-butler::card>
