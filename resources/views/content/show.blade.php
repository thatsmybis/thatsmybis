@extends('layouts.app')
@section('title', $content->title . ' - ' . config('app.name'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-6 offset-lg-3">
            @include('partials/createContent')
        </div>
        <div class="col-12 col-lg-6 offset-lg-3">
            @include('partials/content')
        </div>
    </div>
</div>

@endsection
