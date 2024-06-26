<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('app.name') }}</title>

    <link rel="shortcut icon" href="{{ Vite::asset('resources/images/favicon.ico', 'vendor/butler') }}">

    @stack('scripts')
    @vite(['resources/css/app.css', 'resources/js/app.js'], 'vendor/butler')

    @stack('head')
  </head>
  <body class="bg-gray-100/20 text-gray-500 dark:bg-gray-900 dark:text-white">
    <header class="bg-white dark:bg-gray-800 text-base">
      <section class="flex items-center justify-between border-b border-gray-100 dark:border-gray-500 p-5">
        <h1 class="text-xl">
          <a href="/">{{ config('app.name') }}</a>
        </h1>
        @if(Route::has('auth.redirect'))
          @auth
            <form action="{{ route('auth.logout') }}" method="POST">
              @csrf
              <button class="border border-gray-100 dark:border-gray-500 hover:border-gray-200 dark:hover:border-gray-400 rounded px-3 py-2 flex items-center space-x-2">
                <x-butler::icons.sign-out/>
                <span>Sign out {{ auth()->user()->name }}</span>
              </button>
            </form>
          @else
            <a type="button" href="{{ route('auth.redirect') }}" class="block border border-gray-100 dark:border-gray-500 hover:border-gray-200 dark:hover:border-gray-400 rounded px-3 py-2 flex items-center space-x-2">
              <x-butler::icons.sign-in/>
              <span>Sign in</span>
            </a>
          @endauth
        @endif
      </section>
      <x-butler::navbar/>
    </header>

    @isset($searchbar)
      <div x-data="{query: ''}" class="w-full flex flex-row items-center bg-white dark:bg-gray-700 border-b border-gray-100 dark:border-gray-500">
        <x-butler::icons.search class="ml-5 mr-1"/>
        <input
          type="text"
          class="p-2 w-full outline-none text-black dark:text-white bg-white dark:bg-gray-700"
          x-model="query"
          @keyup.debounce="$dispatch('searchbar', { query })"
          @keydown.escape="query = ''"
          @searchbar-set.window="query = $event.detail?.query || ''"
        />
      </div>
    @endisset

    <main class="p-5">
      @unless(\Butler\Service\Butler::assetsAreCurrent())
        <x-butler::alerts.info>
          The published assets are not up-to-date with the installed version. To update, run
          <code class="text-sm text-zinc-200 border bg-blue-500 border-blue-400 dark:bg-sky-800 dark:border-sky-600 rounded py-1 px-2 mx-1">php artisan butler-service:assets</code>
        </x-butler::alerts.info>
      @endunless

      {{ $slot }}
    </main>

    <div x-cloak x-data="alert" x-bind="bind" class="flex justify-between fixed top-2 inset-x-0 mx-auto p-3 max-w-xl rounded border text-white">
      <span x-text="content"></span>
      <x-butler::icons.close @click="close" class="cursor-pointer"/>
    </div>
  </body>
</html>
