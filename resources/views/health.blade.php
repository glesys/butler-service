@extends('service::layouts.base')

@push('head')
<style>
  .bg-ok { background-color: #68d391; }
  .bg-warning { background-color: #f6ad55; }
  .bg-critical { background-color: #fc8181; }
  .bg-unknown { background-color: #ccc; }
</style>
@endpush

@section('content')

<div class="mt-2">

  <table class="w-full text-xs mb-3">
    <tr>
      <td class="w-1/3 font-bold w-20">PHP</td>
      <td class="w-2/3">{{ $service['php']}}</td>
    </tr>
    <tr>
      <td class="font-bold">Laravel</td>
      <td>{{ $service['laravel']}}</td>
    </tr>
    <tr>
      <td class="font-bold">Butler Service</td>
      <td>{{ $service['butlerService']}}</td>
    </tr>
    <tr>
      <td class="font-bold">Timezone</td>
      <td>{{ $service['timezone']}}</td>
    </tr>
  </table>

  @forelse($checks->groupBy('group')->sortKeys() as $group => $checks)
    <div class="font-bold my-3 text-xs tracking-wide uppercase">{{ Str::title($group) }}</div>
    @foreach($checks as $check)
      <div class="bg-{{ $check['result']->state }} m-1 px-3 py-2 text-xs text-white rounded">
        <abbr title="{{ $check['description'] }}">{{ $check['name'] }}</abbr>
        &ndash; {{ $check['result']->message }}
      </div>
    @endforeach
  @empty
    <div class="bg-blue-400 m-1 px-3 py-2 text-xs text-white rounded">
      No health checks found.
    </div>
  @endforelse

</div>

@endsection
