<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Service Schema</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/vs2015.min.css">
  </head>
  <body class="bg-gray-100">
    <div class="mx-auto py-2 px-8">
      <div>
        <h1 class="mb-2">
          Service Schema
        </h1>
        <pre><code class="graphql rounded">{{ $schema }}</code></pre>
      </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js"></script>
    <script>
      hljs.initHighlightingOnLoad();
      hljs.registerLanguage("graphql",function(e){return{aliases:["gql"],k:{keyword:"query mutation subscription|10 type input schema directive interface union scalar fragment|10 enum on ...",literal:"true false null"},c:[e.HCM,e.QSM,e.NM,{cN:"type",b:"[^\\w][A-Z][a-z]",e:"\\W",eE:!0},{cN:"literal",b:"[^\\w][A-Z][A-Z]",e:"\\W",eE:!0},{cN:"variable",b:"\\$",e:"\\W",eE:!0},{cN:"keyword",b:"[.]{2}",e:"\\."},{cN:"meta",b:"@",e:"\\W",eE:!0}],i:/([;<']|BEGIN)/}});
    </script>
  </body>
</html>
