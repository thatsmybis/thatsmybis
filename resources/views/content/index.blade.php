@extends('layouts.app')
@section('title', 'Resources - ' . config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="col-xs-12 mt-5 mb-5">
        @include('partials/contentList')
    </div>
</div>
@endsection
