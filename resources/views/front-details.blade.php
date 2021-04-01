<div class="bg-white rounded p-4 mb-4">
  <div class="font-bold mb-2 tracking-wide uppercase">Details</div>
  <table class="w-full">
    <tr>
      <td class="w-1/3">PHP</td>
      <td class="w-2/3 font-bold">{{ $service['php'] }}</td>
    </tr>
    <tr>
      <td>Laravel</td>
      <td class="font-bold">{{ $service['laravel'] }}</td>
    </tr>
    <tr>
      <td>Butler Service</td>
      <td class="font-bold">{{ $service['butlerService'] }}</td>
    </tr>
    <tr>
      <td>Timezone</td>
      <td class="font-bold">{{ $service['timezone'] }}</td>
    </tr>
  </table>
</div>
