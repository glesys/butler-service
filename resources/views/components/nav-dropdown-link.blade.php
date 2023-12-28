@props([
  'label',
  'route' => null,
  'active' => false,
])

<li>
  <a {{ $attributes->merge(['href' => $route ? route($route) : false])->class([
    'block px-4 py-2 text-gray-500/70 dark:text-gray-400',
    $active
      ? 'dark:text-white'
      : 'hover:text-gray-400 dark:hover:text-gray-300',
  ]) }}>
    {{ $label }}
  </a>
</li>
