<x-butler-service::front-block
  title="GraphQL"
  http-method="POST"
  response-type="JSON"
  :requires-token="true"
  :url="route('graphql', [], false)"
>
  <div class="font-bold my-3 tracking-wide uppercase text-sm">Authentication</div>
  <div class="xl:w-3/4">
    The only way to authenticate against this service is to
    provide your access token with the request headers;
    <span
      class="bg-secondary rounded p-1 font-mono text-sm text-white whitespace-no-wrap"
    >Authorization: Bearer TOKEN</span>.
  </div>

  <div class="font-bold my-3 tracking-wide uppercase text-sm">Schema</div>
  <pre><code class="graphql rounded">{{ \File::get(config('butler.graphql.schema')) }}</code></pre>
</x-butler-service::front-block>

@push('head')
<link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.4.0/styles/github.min.css" rel="stylesheet">
@endpush

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
