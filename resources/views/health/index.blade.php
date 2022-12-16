<x-butler::layout>
  <x-butler::container>
    <div class="w-full lg:w-1/2">
      @include('butler::health._checks')
    </div>
    <div class="w-full lg:w-1/2">
      @include('butler::health._failed-jobs')
    </div>
  </x-butler::container>
</x-butler::layout>
