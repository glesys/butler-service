@props([
  'color' => 'red',
  'text',
  'dispatch',
  'enabled' => 'true',
  'icon' => 'trash',
  'confirmText' => 'Confirm',
  'confirmIcon' => 'warning-triangle',
])

@php
$classNames = match ($color) {
  'green' => 'bg-green-dark hover:bg-green-dark/90',
  'yellow' => 'bg-yellow-dark hover:bg-yellow-dark/90',
  'red' => 'bg-red-dark hover:bg-red-dark/90',
};
@endphp

<span {{ $attributes }} x-data="{ confirm: false }">
  <x-butler::button
    :icon="$icon"
    ::class="{{ $enabled }} ? '{{ $classNames }}' : 'bg-gray-400 cursor-not-allowed'"
    x-show="! confirm"
    @click="confirm = {{ $enabled }} ? true : false"
  >{{ $text }}</x-butler::button>

  <x-butler::button
    x-cloak
    :icon="$confirmIcon"
    :class="$classNames"
    x-show="confirm"
    @click="$dispatch('{{ $dispatch }}'); confirm = false"
    @click.outside="confirm = false"
  >{{ $confirmText }}</x-butler::button>
</span>
