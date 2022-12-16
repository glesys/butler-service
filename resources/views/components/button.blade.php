@props([
  'icon' => null,
  'lg' => null,
])
<button {{ $attributes->merge(['type' => 'button'])->class([
  'flex items-center text-sm text-white rounded',
  $lg ? 'p-3' : 'px-2 py-1'
]) }}">
  @if($icon)
    <x-dynamic-component :component="'butler::icons.' . $icon" class="w-5 mr-1"/>
  @endif
  <span class="mx-1">{{ $slot }}</span>
</button>
