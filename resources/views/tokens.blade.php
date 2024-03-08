<x-butler::layout>
  <x-butler::container>

    <x-butler::card title="Tokens" class="w-full lg:w-2/3" x-data="tokensCard(
      '{{ route('tokens.index') }}',
      '{{ route('tokens.delete') }}'
    )">
      <x-slot name="buttons">
        <x-butler::confirm-button
          enabled="selectedIds.length"
          dispatch="delete-confirmed"
          text="Delete selected"
          confirmText="Confirm delete"
        />
      </x-slot>

      <x-butler::icons.loading x-show="loading" class="animate-spin"/>

      <div class="text-gray-500/70 dark:text-gray-300 overflow-x-auto">
        <span x-cloak x-show="! loading && ! tokens.length" class="dark:text-white">
          No tokens found.
        </span>

        <table x-cloak x-show="tokens.length" class="w-full">
          <thead>
            <tr class="text-gray-700 dark:text-white text-sm">
              <td class="py-2 px-4 w-32 mt-0.5"><x-butler::checkbox x-bind="selectAllCheckbox"/></td>
              <td class="py-2 px-4">Owner</td>
              <td class="py-2 px-4">Abilities</td>
              <td class="py-2 px-4">Name</td>
              <td class="py-2 px-4">Last used</td>
              <td class="py-2 px-4">Created at</td>
            </tr>
          </thead>
          <tbody class="border-t border-gray-100 dark:border-gray-500">
            <template x-for="token in tokens" :key="token.id">
              <tr class="text-lg border-b border-gray-100 dark:border-gray-500">
                <td class="py-3 px-4"><x-butler::checkbox ::value="token.id" x-model="selectedIds"/></td>
                <td class="py-3 px-4" x-text="token.owner"></td>
                <td class="py-3 px-4" x-text="token.abilities"></td>
                <td class="py-3 px-4" x-text="token.name"></td>
                <td class="py-3 px-4" x-text="token.last_used_at || 'Never used'" :class="{'text-yellow-dark': token.is_stale}"></td>
                <td class="py-3 px-4" x-text="token.created_at"></td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </x-butler::card>

    <x-butler::card title="Create Token" class="w-full lg:w-1/3" x-data="tokenFormCard('{{ route('tokens.store') }}')">
      <x-butler::label>Consumer</x-butler::label>
      <div class="my-4">
        <x-butler::input-text x-model="consumer" placeholder="E.g. email or application name"/>
        <x-butler::error x-show="errors.consumer" x-text="errors.consumer?.[0]"/>
      </div>
      <x-butler::label>Abilities (comma separated)</x-butler::label>
      <div class="my-4">
        <x-butler::input-text x-model="abilities" placeholder="E.g. query,mutation"/>
        <x-butler::error x-show="errors.abilities" x-text="errors.abilities?.[0]"/>
      </div>
      <x-butler::label>Name</x-butler::label>
      <div class="my-4">
        <x-butler::input-text x-model="name" placeholder="Optional name"/>
        <x-butler::error x-show="errors.name" x-text="errors.name?.[0]"/>
      </div>
      <div class="flex justify-end pt-4">
        <x-butler::button
          lg
          class="bg-green-light hover:bg-green-light/90"
          ::disabled="loading"
          @click.prevent="submit"
        >
          Create
        </x-butler::button>
      </div>
    </x-butler::card>

  </x-butler::container>
</x-butler::layout>
