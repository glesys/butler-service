@extends('service::layouts.base')

@section('content')

<h1 class="title text-2xl text-white py-2 ml-4">{{ config('app.name') }}</h1>

<div class="flex flex-col lg:flex-row w-full mx-auto px-4">
  <div class="w-full lg:w-1/3 lg:mr-4">
    @include('service::front-details')
    @include('service::front-health-checks')
    @include('service::front-database-connections')
  </div>

  <div class="w-full lg:w-2/3 mb-2">
    @include('service::front-graphql')
  </div>
</div>
@endsection
