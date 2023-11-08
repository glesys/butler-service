@props([
  'route' => null,
  'active' => false,
])
<a {{ $attributes->merge(['href' => $route ? route($route) : '#'])->class([
  'py-3 px-1 text-gray-500/70 dark:text-gray-400 border-b-2 border-white dark:border-gray-800',
  ($active)
    ? 'dark:text-white border-blue-light'
    : 'hover:text-gray-400 dark:hover:text-gray-300 hover:border-gray-400'
]) }}>{{ $slot }}</a>
