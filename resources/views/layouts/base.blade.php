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
      .bg-primary { background-color: #1b2d3c; }
      .bg-secondary { background-color: #7db9d8; }
      .bg-check-ok { background-color: #68d391; }
      .bg-check-warning { background-color: #f6ad55; }
      .bg-check-critical { background-color: #fc8181; }
      .bg-check-unknown { background-color: #ddd; }
    </style>
    @stack('head')
  </head>
  <body class="bg-primary font-sans">
    @yield('content')
    @stack('scripts')
  </body>
</html>
