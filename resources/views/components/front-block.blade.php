<div {{ $attributes->merge(['class' => 'bg-white rounded p-4 mb-4']) }}>
  <div class="flex flex-row mb-2">
    <div class="flex-1 font-bold tracking-wide uppercase">{{ $title }}</div>
    @if ($url)
    <div class="rounded bg-gray-100 border px-2">
      <a class="text-blue-600" href="{{ $url }}">{{ $url }}</a>

      <span
        class="bg-secondary ml-1 px-1 text-xs text-white rounded cursor-default"
        title="Only {{ $httpMethod }} method allowed"
      >{{ $httpMethod }}</span>

      <span
        class="bg-purple-500 ml-1 px-1 text-xs text-white rounded cursor-default"
        title="Returns {{ $responseType }}"
      >{{ $responseType }}</span>

      @if ($requiresToken)
        <span
          class="bg-purple-500 ml-1 px-1 text-xs text-white rounded cursor-default"
          title="Requires token authentication"
        >token</span>
      @endif
    </div>
    @endif
  </div>
  {{ $slot }}
</div>
