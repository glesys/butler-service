@props(['label'])

<div x-data="{ open: false }" @click.outside="open = false" class="relative cursor-pointer">
  <x-butler::nav-link @click="open = ! open" href="javascript:;" {{ $attributes->class(['inline-flex text-center items-center']) }}>
    <span>{{ $label }}</span>
    <x-butler::icons.carat-down/>
  </x-butler::nav-link>
  <div x-cloak x-show="open" class="absolute top-[54px] z-10 w-max bg-white dark:bg-gray-800 rounded-b-lg border border-gray-100 dark:border-gray-500">
    <ul class="py-2">{{ $slot }}</ul>
  </div>
</div>
