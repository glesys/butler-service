@extends('service::layouts.base')

@section('content')

<div class="flex -m-1">
  <div class="bg-green-400 pill m-1 px-3 py-2 text-xs text-white rounded">GraphQL</div>
  <div class="bg-green-400 pill m-1 px-3 py-2 text-xs text-white rounded">Requires authentication</div>
</div>

<div class="mt-6">
  <div class="font-bold mb-2 text-xs tracking-wide uppercase">Endpoints</div>
  <div>
    <a class="text-blue-600" href="{{ route('graphql') }}">
      {{ route('graphql', [], false) }}
    </a>
    &ndash; Primary GraphQL endpoint for this service.
  </div>
  <div>
    <a class="text-blue-600" href="{{ route('schema') }}">
      {{ route('schema', [], false) }}
    </a>
    &ndash; The current GraphQL schema in use.
  </div>
  <div>
    <a class="text-blue-600" href="{{ route('health') }}">
      {{ route('health', [], false) }}
    </a>
    &ndash; Health checks.
  </div>
</div>

<div class="mt-6">
  <div class="font-bold mb-2 text-xs tracking-wide uppercase">Authentication</div>
  <div>
    The recommended way to authenticate against this service is to
    provide your access token with the request headers, example:
    <span class="bg-yellow-200 p-1 font-mono text-sm">Authorization: Bearer TOKEN</span>.
    <br>
    If headers are unavailable you can also provide your access token in the query,
    example:
    <span class="bg-yellow-200 p-1 font-mono text-sm">
      {{ config('service.routes.graphql', '/graphql') }}?token=TOKEN
    </span>.
  </div>
</div>

@endsection
