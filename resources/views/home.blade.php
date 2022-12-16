<x-butler::layout>
  <x-butler::container class="flex-col-reverse">
    <x-butler::card title="Schema" class="w-full lg:w-2/3">
      <pre><code class="graphql rounded">{{ $graphql }}</code></pre>
    </x-butler::card>

    <x-butler::card title="Authentication" class="w-full lg:w-1/3">
      <p class="mb-6">
        The only way to authenticate against the graphql endpoint is to provide your access token with the request headers.
      </p>

      <pre><code class="bash rounded">curl \
-H "Content-Type: application/json" \
-H "Authorization: Bearer TOKEN" \
-d '{"query": "{ __schema { types { name } } }"}' \
{{ route('graphql') }}
      </code></pre>
    </x-butler::card>
  </x-butler::container>
</x-butler::layout>
