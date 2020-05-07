@extends('service::layouts.base')

@push('head')
<link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.4.0/styles/github.min.css" rel="stylesheet">
@endpush

@section('content')

<h1 class="title text-2xl text-white py-2 ml-4">{{ config('app.name') }}</h1>

<div class="flex flex-col lg:flex-row w-full mx-auto px-4">
  <div class="w-full lg:w-1/3 lg:mr-4">
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

    <div class="bg-white rounded p-4 mb-4">
      <div class="flex flex-row mb-2">
        <div class="flex-1 font-bold tracking-wide uppercase">Health Checks</div>
        <div class="rounded bg-gray-100 border px-2">
          <a class="text-blue-600" href="{{ route('health') }}">{{ route('health', [], false) }}</a>
          <span
            class="bg-secondary ml-1 px-1 text-xs text-white rounded cursor-default"
            title="Only GET method allowed"
          >GET</span>
          <span
            class="bg-purple-500 ml-1 px-1 text-xs text-white rounded cursor-default"
            title="Returns JSON"
          >JSON</span>
        </div>
      </div>

      @forelse($checks->groupBy('group')->sortKeys() as $group => $checks)
        <div class="font-bold my-3 text-sm tracking-wide uppercase">{{ Str::title($group) }}</div>
        @foreach($checks as $check)
          <div class="bg-check-{{ $check['result']->state }} m-1 px-3 py-2 text-sm text-white rounded">
            <abbr title="{{ $check['description'] }}">{{ $check['name'] }}</abbr>
            &ndash; {{ $check['result']->message }}
          </div>
        @endforeach
      @empty
        <div class="bg-secondary m-1 px-3 py-2 text-sm text-white rounded">
          No health checks found.
        </div>
      @endforelse
    </div>
  </div>

  <div class="w-full lg:w-2/3 mb-2">
    <div class="bg-white rounded p-4 mb-4">
      <div class="flex flex-row mb-2">
        <div class="flex-1 font-bold tracking-wide uppercase">GraphQL</div>
        <div class="rounded bg-gray-100 border px-2">
          <a class="text-blue-600" href="{{ route('graphql') }}">{{ route('graphql', [], false) }}</a>
          <span
            class="bg-secondary ml-1 px-1 text-xs text-white rounded cursor-default"
            title="Only POST method allowed"
          >POST</span>
          <span
            class="bg-purple-500 ml-1 px-1 text-xs text-white rounded cursor-default"
            title="Requires authentication with JWT"
          >JWT</span>
        </div>
      </div>

      <div class="font-bold my-3 tracking-wide uppercase text-sm">Authentication</div>
      <div class="xl:w-3/4">
        The recommended way to authenticate against this service is to
        provide your access token with the request headers;
        <span
          class="bg-secondary rounded p-1 font-mono text-sm text-white whitespace-no-wrap"
        >Authorization: Bearer TOKEN</span>.
        <br>
        If headers are unavailable you can also provide your access token in the query;
        <span
          class="bg-secondary rounded p-1 font-mono text-sm text-white whitespace-no-wrap"
        >{{ route('graphql', [], false) }}?token=TOKEN</span>.
      </div>

      <div class="font-bold my-3 tracking-wide uppercase text-sm">Schema</div>
      <pre><code class="graphql rounded">{{ \File::get(config('butler.graphql.schema')) }}</code></pre>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.4.0/highlight.min.js"></script>
<script>
  hljs.initHighlightingOnLoad();
  hljs.registerLanguage("graphql",function (e) {
    return {
      aliases: ["gql"],
      keywords: {
        keyword: "query mutation subscription|10 type input schema directive interface union scalar fragment|10 enum on ...",
        literal: "true false null"
      },
      contains: [
        e.HASH_COMMENT_MODE,
        e.QUOTE_STRING_MODE,
        e.NUMBER_MODE,
        {
          className: "type",
          begin: "[^\\w][A-Z][a-z]",
          end: "\\W",
          excludeEnd: !0
        },
        {
          className: "literal",
          begin: "[^\\w][A-Z][A-Z]",
          end:"\\W",
          excludeEnd: !0
        },
        {
          className: "variable",
          begin: "\\$",
          end:"\\W",
          excludeEnd: !0
        },
        {
          className: "keyword",
          begin: "[.]{2}",
          end: "\\."
        },
        {
          className: "meta",
          begin: "@",
          end: "\\W",
          excludeEnd: !0
        }
      ],
      illegal: /([;<']|BEGIN)/
    }
  });
</script>
@endpush
