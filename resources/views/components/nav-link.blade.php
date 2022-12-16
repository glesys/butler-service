@props([
  'route' => null,
  'active' => false,
])
<a {{ $attributes->merge(['href' => $route ? route($route) : '#'])->class([
  'py-3 px-1 text-gray-500/70 dark:text-gray-400',
  ($active)
    ? 'dark:text-white border-b-2 border-blue-light'
    : 'hover:text-gray-400 dark:hover:text-gray-300 hover:border-b-2 border-gray-400'
]) }}>{{ $slot }}</a>
