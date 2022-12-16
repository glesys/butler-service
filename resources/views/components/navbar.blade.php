<nav x-data="navbar" class="text-lg border-b border-gray-100 dark:border-gray-500">
  <div class="flex flex-row gap-5 px-5 -mb-px">
    <x-butler::nav-link route="home" :active="request()->routeIs('home')">
      GraphQL
    </x-butler::nav-link>
    <x-butler::nav-link route="about" :active="request()->routeIs('about')">
      About
    </x-butler::nav-link>
    <x-butler::nav-link route="health" :active="request()->routeIs('health') || request()->is('failed-jobs*')">
      <span x-bind="healthLink">Health</span>
    </x-butler::nav-link>
    @auth
      <x-butler::nav-link route="tokens.index" :active="request()->is('tokens*')">
        Tokens
      </x-butler::nav-link>
    @else
      <span class="block text-gray-300 dark:text-gray-500 cursor-not-allowed py-3 px-1">
        Tokens
      </span>
    @endauth
  </div>
</nav>
