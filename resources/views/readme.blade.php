<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <style>
      body { background-color: #1b2d3c; }
      .title { color: #1b2d3c; }
      .pill {
        background-color: #63cc98;
        pointer-events: none;
      }
    </style>
  </head>
  <body class="font-sans">
    <div class="max-w-xl mx-auto px-4 py-8">
      <div class="bg-white rounded p-8">

        <h1 class="title mb-2 text-2xl">
          {{ config('app.name') }}
          <span class="font-mono font-normal ml-2 text-xs">{{ config('app.version') }}</span>
        </h1>

        <div class="flex -m-1">
          <div class="pill m-1 px-3 py-2 text-xs text-white rounded">GraphQL</div>
          <div class="pill m-1 px-3 py-2 text-xs text-white rounded">Requires authentication</div>
        </div>

        <div class="mt-6">
          <div class="font-bold mb-2 text-xs tracking-wide uppercase">Endpoints</div>
          <div>
            <a class="text-blue-600" href="{{ route('graphql') }}">
              {{ config('service.routes.graphql', '/graphql') }}
            </a>
            &ndash; Primary GraphQL endpoint for this service
          </div>
          <div>
            <a class="text-blue-600" href="{{ route('schema') }}">
              {{ config('service.routes.schema', '/schema') }}
            </a>
            &ndash; The current GraphQL schema in use.
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

      </div>
    </div>
  </body>
</html>
