@props(['title', 'buttons'])
<div {{ $attributes->class('border border-gray-100 dark:border-gray-500 bg-white dark:bg-gray-800 rounded mb-5') }}>

  <div class="flex items-center justify-between h-14 py-3 px-4 -mb-2 border-b border-gray-100 dark:border-gray-500">
    <x-butler::h1>{{ $title }}</x-butler::h1>

    @isset($buttons)
      <div {{ $buttons->attributes->merge(['class' => 'flex gap-3']) }}>{{ $buttons }}</div>
    @endisset
  </div>

  <div class="p-8 mb-1">{{ $slot }}</div>
</div>
