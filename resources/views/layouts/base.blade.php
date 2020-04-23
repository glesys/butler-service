<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <style>
      body { background-color: #1b2d3c; }
      .title { color: #1b2d3c; }
    </style>
    @stack('head')
  </head>
  <body class="font-sans">
    <div class="max-w-xl mx-auto px-4 py-8">
      <div class="bg-white rounded p-8">
        <h1 class="title mb-2 text-2xl">{{ config('app.name') }}</h1>
        @yield('content')
      </div>
    </div>
  </body>
</html>
