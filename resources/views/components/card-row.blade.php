@props(['label', 'value' => ''])

<div class="flex flex-row justify-between border-b dark:border-gray-600 py-1">
  <x-butler::label class="mr-1">{{ $label }}</x-butler::label>
  @if($slot->isNotEmpty())
    {{ $slot }}
  @else
    <x-butler::muted {{ $attributes }}>{{ $value }}</x-butler::muted>
  @endif
</div>
