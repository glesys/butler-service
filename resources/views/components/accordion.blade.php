@props(['content'])
<div
  x-data="{ expand: false }"
  @click="expand = ! expand"
  class="p-4 border border-gray-100 bg-gray-100/20 dark:border-gray-700 dark:bg-gray-700 rounded"
>
  <div class="flex items-center justify-between cursor-pointer">
    <div class="flex items-center">
        {{ $slot }}
    </div>
    <div class="dark:text-white">
      <x-butler::icons.chevron-down x-cloak x-show="! expand"/>
      <x-butler::icons.chevron-up x-cloak x-show="expand"/>
    </div>
  </div>
  <div x-cloak x-show="expand" class="mt-4">
      {{ $content }}
  </div>
</div>
