<x-butler-service::front-block title="Details">
  <table class="w-full">
    <tr>
      <td class="w-1/3">PHP</td>
      <td class="w-2/3 font-bold">{{ $application['php'] }}</td>
    </tr>
    <tr>
      <td>Laravel</td>
      <td class="font-bold">{{ $application['laravel'] }}</td>
    </tr>
    @if ($application['runningOctane'] ?? false)
      <tr>
        <td>Laravel Octane</td>
        <td class="font-bold">
          {{ $application['laravelOctane'] }}
          <span class="font-normal text-green-500">(running)</span>
        </td>
      </tr>
    @endif
    <tr>
      <td>Butler Service</td>
      <td class="font-bold">{{ $application['butlerService'] }}</td>
    </tr>
    <tr>
      <td>Timezone</td>
      <td class="font-bold">{{ $application['timezone'] }}</td>
    </tr>
  </table>
</x-butler-service::front-block>
