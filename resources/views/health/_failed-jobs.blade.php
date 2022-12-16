<x-butler::card
  title="Failed Jobs"
  x-data="failedJobsCard(
    '{{ route('failed-jobs.index') }}',
    '{{ route('failed-jobs.retry') }}',
    '{{ route('failed-jobs.forget') }}',
  )"
>
  @auth
    <x-slot name="buttons">
      <x-butler::confirm-button
        enabled="selectedIds.length"
        dispatch="retry-confirmed"
        text="Retry selected"
        icon="retry"
        confirmText="Confirm retry"
        color="yellow"
      />

      <x-butler::confirm-button
        enabled="selectedIds.length"
        dispatch="forget-confirmed"
        text="Forget selected"
        confirmText="Confirm forget"
      />
    </x-slot>
  @endauth

  <div class="text-gray-500/70 dark:text-gray-300 overflow-x-auto">
    <div x-cloak x-show="! failedJobs.length">
      <span x-show="error" class="text-red-dark" x-text="error"></span>
      <span x-show="! error">No failed jobs.</span>
    </div>

    <table x-cloak x-show="failedJobs.length" class="w-full">
      <thead>
        <tr class="text-gray-700 dark:text-white text-sm">
          @auth
            <td class="py-2 px-4"><x-butler::checkbox x-bind="selectAllCheckbox" zclick="selectAll"/></td>
          @endauth
          <td class="py-2 px-4">Name</td>
          <td class="py-2 px-4">Failed at</td>
        </tr>
      </thead>
      <tbody class="border-t border-gray-100 dark:border-gray-500">
        <template x-for="failedJob in failedJobs" :key="failedJob.id">
          <tr class="text-lg border-b border-gray-100 dark:border-gray-500">
            @auth
              <td class="py-3 px-4"><x-butler::checkbox ::value="failedJob.id" x-model="selectedIds"/></td>
            @endauth
            <td class="py-3 px-4">
              @auth
                <a class="text-blue-light" :href="failedJob.url" x-text="failedJob.name"></a>
              @else
                <span x-text="failedJob.name"></span>
              @endauth
            </td>
            <td class="py-3 px-4" x-text="failedJob.failedAt"></td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</x-butler::card>
