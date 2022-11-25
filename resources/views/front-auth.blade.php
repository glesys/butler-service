@guest
  <x-butler-service::front-block title="Sign In">
    <form action="{{ route('auth.redirect') }}" method="GET">
      <button type="submit" class="bg-secondary hover:bg-blue-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        Sign In
      </button>
    </form>
  </x-butler-service::front-block>
@else
  <x-butler-service::front-block title="Sign Out">
    <form action="{{ route('auth.logout') }}" method="POST">
      @csrf
      <button type="submit" class="bg-secondary hover:bg-blue-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        Sign out {{ Auth::user()->username }}
      </button>
    </form>
  </x-butler-service::front-block>
@endguest
