@props([
  'title',
  'buttons',
  'noWrap' => false,
  'noPadding' => false,
])

<div {{ $attributes->class(['border border-gray-100 dark:border-gray-500 bg-white dark:bg-gray-800 rounded mb-5']) }}>
  <div class="flex items-center justify-between h-14 py-3 px-4 border-b border-gray-100 dark:border-gray-500">
    <x-butler::h1>{{ $title }}</x-butler::h1>

    @isset($buttons)
      <div {{ $buttons->attributes->merge(['class' => 'flex gap-3']) }}>{{ $buttons }}</div>
    @endisset
  </div>

  <div @class([
    'whitespace-nowrap overflow-x-auto scrollbar-thin scrollbar-track-rounded scrollbar-thumb-rounded scrollbar-thumb-gray-400 scrollbar-track-white dark:scrollbar-thumb-gray-900 dark:scrollbar-track-gray-800' => $noWrap,
  ])>
    <div @class(['mb-1', 'p-8' => $noPadding === false])>{{ $slot }}</div>
  </div>
</div>
