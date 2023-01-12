<x-butler-service::front-block title="Details">
  <table class="w-full">
    <tr>
      <td class="w-1/3">PHP</td>
      <td class="w-2/3 font-bold">{{ $about['environment']['php_version'] }}</td>
    </tr>
    <tr>
      <td>Laravel</td>
      <td class="font-bold">{{ $about['environment']['laravel_version'] }}</td>
    </tr>
    @if($about['laravel_octane']['running'] ?? false)
      <tr>
        <td>Laravel Octane</td>
        <td class="font-bold">
          {{ $about['laravel_octane']['version'] }}
          <span class="font-normal text-green-500">(running)</span>
        </td>
      </tr>
    @endif
    <tr>
      <td>Butler Service</td>
      <td class="font-bold">{{ $about['butler_service']['version'] }}</td>
    </tr>
    <tr>
      <td>Timezone</td>
      <td class="font-bold">{{ $about['environment']['timezone'] }}</td>
    </tr>
  </table>
</x-butler-service::front-block>
